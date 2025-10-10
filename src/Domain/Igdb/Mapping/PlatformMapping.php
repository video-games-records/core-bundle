<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Domain\Igdb\Mapping;

/**
 * Static mapping between VGR Platform IDs and IGDB Platform IDs
 */
class PlatformMapping extends AbstractMapping
{
    /**
     * Mapping from VGR Platform ID to IGDB Platform ID
     * Based on the data provided: VGR ID => IGDB ID
     */
    private const VGR_TO_IGDB_MAPPING = [
        1 => 21,   // GameCube => Nintendo GameCube
        2 => 8,    // PlayStation 2 => PlayStation 2
        3 => 11,   // Xbox => Xbox
        4 => 23,   // Dreamcast => Dreamcast
        5 => 32,   // Saturn => Sega Saturn
        6 => 7,    // PlayStation => PlayStation
        7 => 24,   // Game Boy Advance => Game Boy Advance
        8 => 19,   // Super Nintendo / Super Famicom => Super Nintendo Entertainment System
        9 => 33,   // Game Boy / Color => Game Boy
        10 => 20,  // Nintendo Dual Screen => Nintendo DS
        11 => 4,   // Nintendo 64 => Nintendo 64
        12 => 80,  // Neo Geo => Neo Geo AES
        13 => 6,   // PC => PC (Microsoft Windows)
        14 => 12,  // Xbox 360 => Xbox 360
        15 => 5,   // Wii => Wii
        16 => 9,   // PlayStation 3 => PlayStation 3
        17 => 18,  // Nintendo / Famicom => Nintendo Entertainment System
        18 => 64,  // Master System => Sega Master System/Mark III
        19 => 38,  // PlayStation Portable => PlayStation Portable
        20 => 29,  // Megadrive / Genesis => Sega Mega Drive/Genesis
        21 => 86,  // PC-Engine / TurboGrafx => TurboGrafx-16/PC Engine
        22 => 59,  // Atari 2600 => Atari 2600
        24 => 62,  // Jaguar => Atari Jaguar
        27 => 68,  // Colecovision => ColecoVision
        28 => 16,  // Amiga => Amiga
        29 => 307, // Game & Watch => Game & Watch
        30 => 66,  // Atari 5200 => Atari 5200
        31 => 87,  // Virtual Boy => Virtual Boy
        33 => 57,  // WonderSwan => WonderSwan
        34 => 15,  // Commodore 64 => Commodore C64/128/MAX
        35 => 35,  // Game Gear => Sega Game Gear
        36 => 50,  // 3DO Interactive Multiplayer => 3DO Interactive Multiplayer
        37 => 27,  // MSX => MSX
        38 => 61,  // Lynx => Atari Lynx
        39 => 120, // Neo Geo Pocket Color => Neo Geo Pocket Color
        40 => 37,  // Nintendo 3DS => Nintendo 3DS
        41 => 39,  // iOS => iOS
        42 => 34,  // Android => Android
        43 => 74,  // Windows Phone => Windows Phone
        44 => 89,  // Microvision => Microvision
        45 => 41,  // Wii U => Wii U
        46 => 25,  // Amstrad CPC6128 => Amstrad CPC
        47 => 46,  // PS Vita => PlayStation Vita
        48 => 6,   // Windows 8 => PC (Microsoft Windows)
        49 => 48,  // PlayStation 4 => PlayStation 4
        50 => 49,  // Xbox One => Xbox One
        51 => 6,   // Windows 10 => PC (Microsoft Windows)
        52 => 130, // Switch => Nintendo Switch
        53 => 26,  // ZX spectrum => ZX Spectrum
        54 => 63,  // Atari ST => Atari ST/STE
        55 => 121, // Sharp x68000 => Sharp X68000
        56 => 53,  // MSX2 => MSX2
        57 => 65,  // Atari 400/800/1200/XL/XE => Atari 8-bit
        58 => 29,  // Megadrive Classic => Sega Mega Drive/Genesis
        59 => null, // Bandai Electronics => No direct IGDB mapping
        60 => 70,  // Vectrex => Vectrex
        61 => 60,  // Atari 7800 => Atari 7800
        62 => 169, // Xbox Series => Xbox Series X|S
        63 => 167, // PlayStation 5 => PlayStation 5
        64 => 29,  // Megadrive / Genesis Mini => Sega Mega Drive/Genesis
        65 => 19,  // Super Nintendo / Super Famicom Mini => Super Nintendo Entertainment System
        66 => 18,  // Nintendo / Famicom Mini => Nintendo Entertainment System
        67 => 7,   // PlayStation Classic => PlayStation
        68 => 15,  // Commodore 64 Mini => Commodore C64/128/MAX
        69 => 80,  // Neo Geo Mini => Neo Geo AES
        70 => 80,  // Neo Geo Arcade Stick Pro => Neo Geo AES
        71 => 78,  // Mega-CD / Sega CD => Sega CD
        72 => 309, // Evercade => Evercade
        73 => 29,  // Megadrive/Genesis Mini 2 => Sega Mega Drive/Genesis
        74 => 126, // TRS-80 => TRS-80
        75 => 508, // Switch 2 => Nintendo Switch 2
    ];

    /**
     * Get IGDB Platform ID from VGR Platform ID
     */
    public static function getIgdbId(int $vgrPlatformId): ?int
    {
        return self::VGR_TO_IGDB_MAPPING[$vgrPlatformId] ?? null;
    }


    /**
     * Get the complete mapping array
     */
    public static function getMapping(): array
    {
        return self::VGR_TO_IGDB_MAPPING;
    }

}