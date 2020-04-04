<?php
namespace VideoGamesRecords\CoreBundle\Command;

use ProjetNormandie\CommonBundle\Command\DefaultCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PictureCommand extends DefaultCommand
{
    protected function configure()
    {
        $this
            ->setName('vgr-core:picture')
            ->setDescription('Command for picture')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'Who do you want to do?'
            )
            ->addOption(
                'user',
                null,
                InputOption::VALUE_REQUIRED,
                ''
            )
            ->addOption(
                'password',
                null,
                InputOption::VALUE_REQUIRED,
                ''
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $function = $input->getArgument('function');
        switch ($function) {
            case 'migrate-to-amazon':
                $user = $input->getOption('user');
                $password = $input->getOption('password');

                if ($user && $password) {
                    $db = new \Pdo('mysql:dbname=vgrpicture;host=localhost', $user, $password);
                    $sth = $db->prepare('
                        SELECT `blob`, idMembre as idPlayer, idGame, idPicture, type, t_picture.name as name
                        FROM t_picture 
                        INNER JOIN vgr.vgr_chart ON vgr_chart.id = t_picture.idRecord
                        INNER JOIN vgr.vgr_group ON vgr_group.id = vgr_chart.idGroup
                        WHERE idPicture NOT IN (SELECT id FROM vgr.vgr_picture)
                        ');
                    $sth->execute();
                    $list = $sth->fetchAll();

                    $s3 = $this->getContainer()->get('aws.s3');
                    foreach ($list as $row) {
                        $fileInfo = pathinfo($row['name']);
                        $metadata = [
                            'idplayer' => $row['idPlayer'],
                            'idgame' => $row['idGame']
                        ];

                        $key = $row['idPlayer'] . '/' . $row['idGame'] . '/' . uniqid() . '.' . $fileInfo['extension'];

                        // Calculate hash
                        $fileName = 't_picture.' . $fileInfo['extension'];
                        $file = fopen($fileName, "w");
                        fwrite($file, $row['blob']);
                        fclose($file);
                        $hash = hash_file('sha256', $fileName);

                        $s3->putObject(
                            [
                                'Bucket' => $_ENV['AWS_BUCKET_PROOF'],
                                'Key' => $key,
                                'Body' => $row['blob'],
                                'ACL' => 'public-read',
                                'ContentType' => $row['type'],
                                'Metadata' => $metadata,
                                'StorageClass' => 'STANDARD',
                            ]
                        );

                        $query = 'INSERT INTO vgr.vgr_picture (id, path, hash, metadata, idPlayer, idGame) VALUES (?,?,?,?,?,?)';
                        $serializeMetadata = serialize($metadata);
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(1, $row['idPicture']);
                        $stmt->bindParam(2, $key);
                        $stmt->bindParam(3, $hash);
                        $stmt->bindParam(4, $serializeMetadata);
                        $stmt->bindParam(5, $row['idPlayer']);
                        $stmt->bindParam(6, $row['idGame']);
                        $stmt->execute();
                    }
                } else {
                    echo 'Missing user & password database';
                }
                break;
        }
        return true;
    }
}
