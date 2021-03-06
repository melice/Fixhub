<?php

/*
 * This file is part of Fixhub.
 *
 * Copyright (C) 2016 Fixhub.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fixhub\Composers;

use Fixhub\Models\Deployment;
use Illuminate\Contracts\View\View;

/**
 * View composer for the header bar.
 */
class HeaderComposer
{
    /**
     * Generates the pending and deploying projects for the view.
     *
     * @param  \Illuminate\Contracts\View\View $view
     * @return void
     */
    public function compose(View $view)
    {
        $pending = $this->getPending();

        $view->with('pending', $pending);
        $view->with('pending_count', count($pending));

        $deploying = $this->getRunning();

        $view->with('deploying', $deploying);
        $view->with('deploying_count', count($deploying));
    }

    /**
     * Gets pending deployments.
     *
     * @return array
     */
    private function getPending()
    {
        return $this->getStatus(Deployment::PENDING);
    }

    /**
     * Gets running deployments.
     *
     * @return array
     */
    private function getRunning()
    {
        return $this->getStatus(Deployment::DEPLOYING);
    }

    /**
     * Gets deployments with a supplied status.
     *
     * @param  int   $status
     * @return array
     */
    private function getStatus($status)
    {
        $raw_sql = 'project_id IN (SELECT id FROM projects WHERE deleted_at IS NULL)';

        return Deployment::whereRaw($raw_sql)
                           ->where('status', $status)
                           ->whereNotNull('started_at')
                           ->orderBy('started_at', 'DESC')
                           ->get();
    }
}
