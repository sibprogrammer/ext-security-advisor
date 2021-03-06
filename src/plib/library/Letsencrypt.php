<?php

class Modules_SecurityAdvisor_Letsencrypt
{
    const INSTALL_URL = 'https://ext.plesk.com/packages/f6847e61-33a7-4104-8dc9-d26a0183a8dd-letsencrypt/download';

    public static function isCertificate($certificateName)
    {
        return 0 === strpos($certificateName, 'Lets Encrypt ');
    }

    public static function run($domainName, $securePanel = false)
    {
        $options = ['-d', $domainName];
        if ($securePanel) {
            $options[] = '--letsencrypt-plesk:plesk-secure-panel';
        }
        $result = pm_ApiCli::callSbin('letsencrypt.sh', $options);
        if ($result['code']) {
            throw new pm_Exception("{$result['stdout']}\n{$result['stderr']}");
        }
    }

    public static function countInsecureDomains()
    {
        return pm_Bootstrap::getDbAdapter()->fetchOne("SELECT COUNT(*) FROM hosting WHERE certificate_id = 0");
    }
}
