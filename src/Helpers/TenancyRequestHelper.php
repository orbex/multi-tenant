<?php

namespace Hyn\MultiTenant\Helpers;

use App;
use Illuminate\Database\QueryException;
use Hyn\MultiTenant\Contracts\HostnameRepositoryContract;
use Request;

/**
 * Class TenancyRequestHelper.
 *
 * Helper class to identify requested hostname and website
 */
abstract class TenancyRequestHelper
{
    /**
     * Loads Hostname models based on request.
     *
     * @param HostnameRepositoryContract $hostname
     *
     * @return \Hyn\MultiTenant\Models\Hostname
     */
    public static function hostname(HostnameRepositoryContract $hostname)
    {
        $tenant_hostname = null;

        try {
            if (! App::runningInConsole()) {
                $tenant_hostname = $hostname->findByHostname(Request::getHost());
            }

            if (! $tenant_hostname) {
                $tenant_hostname = $hostname->getDefault();
            }
        } catch (QueryException $e) {
            // table not found, set up not yet done
            if (preg_match('/\Qtable or view not found\E/', $e->getMessage())) {
                return;
            }
        }

        return $tenant_hostname;
    }
}
