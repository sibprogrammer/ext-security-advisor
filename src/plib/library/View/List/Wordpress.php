<?php

class Modules_SecurityAdvisor_View_List_Wordpress extends pm_View_List_Simple
{
    protected function _init()
    {
        parent::_init();

        $this->setData($this->_fetchData());
        $this->setColumns($this->_getColumns());
        $this->setTools($this->_getTools());
    }

    private function _fetchData()
    {
        $db = pm_Bootstrap::getDbAdapter();
        $allWp = $db->query("SELECT * FROM WordpressInstances");
        $wordpress = [];
        foreach ($allWp as $wp) {
            if (pm_Session::getClient()->hasAccessToDomain($wp['subscriptionId'])) {
                //continue;
            }
            $allProperties = $db->query("SELECT * FROM WordpressInstanceProperties WHERE wordpressInstanceId = ?", [$wp['id']]);
            $properties = [];
            foreach ($allProperties as $p) {
                $properties[$p['name']] = $p['value'];
            }
            if (0 === strpos($properties['url'], 'https://')) {
                $httpsImage = 'https-enabled.png';
                $httpsImageAlt = 'enabled';
                $httpsImageTitle = $this->lmsg('list.wordpress.httpsEnableTitle');
            } else {
                $httpsImage = 'https-disabled.png';
                $httpsImageAlt = 'disabled';
                $httpsImageTitle = $this->lmsg('list.wordpress.httpsDisableTitle');
            }

            $wordpress[] = [
                'id' => $wp['id'],
                'name' => $properties['name'],
                'url' => '<a href="' . $this->_view->escape($properties['url']) . '" target="_blank">' . $this->_view->escape($properties['url']) . '</a>',
                'onHttps' => '<img src="' . pm_Context::getBaseUrl() . '/images/' . $httpsImage . '" alt="' . $httpsImageAlt . '" title="' . $httpsImageTitle . '">'
                                . ' ' . $httpsImageTitle,
            ];
        }
        return $wordpress;
    }

    private function _getColumns()
    {
        return [
            pm_View_List_Simple::COLUMN_SELECTION,
            'name' => [
                'title' => $this->lmsg('list.wordpress.nameColumn'),
                'noEscape' => false,
                'searchable' => true,
            ],
            'url' => [
                'title' => $this->lmsg('list.wordpress.urlColumn'),
                'noEscape' => true,
            ],
            'onHttps' => [
                'title' => $this->lmsg('list.wordpress.httpsColumn'),
                'noEscape' => true,
            ],
        ];
    }

    private function _getTools()
    {
        return [
            [
                'title' => $this->lmsg('list.wordpress.switchToHttpsButtonTitle'),
                'description' => $this->lmsg('list.wordpress.switchToHttpsButtonDesc'),
                'link' => pm_Context::getActionUrl('index', 'switch-wordpress-to-https'),
                'execGroupOperation' => [
                    'url' => pm_Context::getActionUrl('index', 'switch-wordpress-to-https'),
                ],
            ],
        ];
    }
}
