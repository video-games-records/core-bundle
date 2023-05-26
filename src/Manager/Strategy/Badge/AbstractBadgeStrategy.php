<?php

namespace VideoGamesRecords\CoreBundle\Manager\Strategy\Badge;

use Symfony\Contracts\Translation\TranslatorInterface;
use VideoGamesRecords\CoreBundle\Contracts\BadgeInterface;

abstract class AbstractBadgeStrategy implements BadgeInterface
{
    private TranslatorInterface $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return TranslatorInterface
     */
    protected function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }


    /**
     * @param Contribution $rule
     *
     * @return string
     *
     * @throws \UnexpectedValueException
     */
    protected function extractRuleProductType(Contribution $rule): string
    {
        $ruleProductType = $rule->getProductType();

        if (!array_key_exists($ruleProductType, $this->productTypeMapping)) {
            throw new \UnexpectedValueException(sprintf('Could not extract product type from %s', $ruleProductType));
        }

        $category = $this->productTypeMapping[$ruleProductType];

        if (!array_key_exists($category, $this->productCategoryMapping)) {
            throw new \UnexpectedValueException(sprintf('Could not get type for category %s', $category));
        }

        return $this->productCategoryMapping[$category];
    }

    /**
     * @param int    $companyId
     * @param bool   $isInternal
     * @param string $ruleType
     * @param string $campaignType
     * @param string $productType
     *
     * @return int
     *
     * @throws DuplicateGlobalRuleException
     */
    protected function insertBaseRule(
        int $companyId,
        bool $isInternal,
        string $ruleType,
        string $campaignType = self::TYPE_ALL,
        string $productType = self::TYPE_ALL
    ): int
    {
        try {

            $this->connection->executeStatement(
                'INSERT INTO rule_company
                    (company_id, campaign_type, product_type, is_internal, rule_type)
                    VALUES (:companyId, :campaignType, :productType,  :isInternal, :ruleType)',
                [
                    'companyId'    => $companyId,
                    'campaignType' => $campaignType,
                    'productType'  => $productType,
                    'isInternal'   => $isInternal,
                    'ruleType'     => $ruleType,
                ],
                [
                    'isInternal' => 'boolean',
                ]
            );

        } catch (UniqueConstraintViolationException $e) {
            throw new DuplicateGlobalRuleException(sprintf('Same rule already exists for company %d', $companyId));
        }

        return (int) $this->connection->lastInsertId();
    }

    /**
     * @param int         $companyId
     * @param string      $campaignType
     * @param string      $productType
     * @param string|null $ruleType
     *
     * @return int
     */
    protected function getRuleId(int $companyId, string $campaignType, string $productType, ?string $ruleType): int
    {
        $sql = 'SELECT
            id
            FROM rule_company
            WHERE
                company_id = :companyId
                AND campaign_type = :campaignType
                AND product_type = :productType
        ';

        $params = [
            'companyId'    => $companyId,
            'campaignType' => $campaignType,
            'productType'  => $productType,
        ];

        if ($ruleType !== null) {
            $sql .= ' AND rule_type = :ruleType';
            $params['ruleType'] = $ruleType;
        }

        return (int) $this->connection->executeQuery($sql, $params)->fetchOne();
    }

    /**
     * @param int $companyId
     *
     * @return bool
     */
    protected function companyHasGlobalRule(int $companyId, string $ruleType = null): bool
    {
        $ruleId = $this->getRuleId($companyId, self::TYPE_ALL, self::TYPE_ALL, $ruleType);

        return $ruleId > 0;
    }
}