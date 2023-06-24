<?php

namespace VideoGamesRecords\CoreBundle;


final class VideoGamesRecordsCoreEvents
{
    public const SCORES_PLAYER_MAJ_COMPLETED = 'vgr_core.scores.player.maj.completed';
    public const SCORES_TEAM_MAJ_COMPLETED = 'vgr_core.scores.team.maj.completed';
    #RANKING
    public const PLAYER_GAME_MAJ_COMPLETED = 'vgr_core.player.game.maj.completed';
    public const PLATFORM_MAJ_COMPLETED = 'vgr_core.platform.maj.completed';
    public const TEAM_GAME_MAJ_COMPLETED = 'vgr_core.team.game.maj.completed';
    public const PLAYER_MAJ_COMPLETED = 'vgr_core.player.maj.completed';
    public const TEAM_MAJ_COMPLETED = 'vgr_core.team.maj.completed';
    public const COUNTRY_MAJ_COMPLETED = 'vgr_core.country.maj.completed';
    public const SCORE_PLATFORM_UPDATED = 'vgr_core.score.platform.updated';
    public const PLAYER_CHART_MAJ_COMPLETED = 'vgr_core.player.chart.maj.completed';
    #GAME
    public const GAME_PUBLISHED = 'vgr_core.game.published';
    #PROOF
    public const PROOF_ACCEPTED = 'vgr_core.proof.accepted';
    public const PROOF_REFUSED = 'vgr_core.proof.refused';
    public const PROOF_REQUEST_ACCEPTED = 'vgr_core.proof.request.accepted';
    public const PROOF_REQUEST_REFUSED = 'vgr_core.proof.request.refused';
    #BADGE
    public const PLAYER_BADGE_LOST = 'vgr_core.player.badge.lost';
    public const TEAM_BADGE_LOST = 'vgr_core.team.badge.lost';
}
