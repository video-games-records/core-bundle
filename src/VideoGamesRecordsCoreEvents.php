<?php

namespace VideoGamesRecords\CoreBundle;


final class VideoGamesRecordsCoreEvents
{
    public const PLAYER_GAME_MAJ_COMPLETED = 'vgr_core.player.game.maj.completed';
    public const GAME_PUBLISHED = 'vgr_core.game.published';
    public const PLATFORM_MAJ_COMPLETED = 'vgr_core.platform.maj.completed';
    public const TEAM_GAME_MAJ_COMPLETED = 'vgr_core.team.game.maj.completed';
    public const PLAYER_MAJ_COMPLETED = 'vgr_core.player.maj.completed';
    public const TEAM_MAJ_COMPLETED = 'vgr_core.team.maj.completed';
    public const COUNTRY_MAJ_COMPLETED = 'vgr_core.country.maj.completed';
    public const SCORE_PLATFORM_UPDATED = 'vgr_core.score.platform.updated';
    public const PLAYER_CHART_MAJ_COMPLETED = 'vgr_core.player.chart.maj.completed';
    public const PROOF_ACCEPTED = 'vgr_core.proof.accepted';
    public const PROOF_REFUSED = 'vgr_core.proof.refused';
    public const PROOF_REQUEST_ACCEPTED = 'vgr_core.proof.request.accepted';
    public const PROOF_REQUEST_REFUSED = 'vgr_core.proof.request.refused';
}
