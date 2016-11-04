<?php

/*
 * This file is part of the "PHP Redis Admin" package.
 *
 * (c) Faktiva (http://faktiva.com)
 *
 * NOTICE OF LICENSE
 * This source file is subject to the CC BY-SA 4.0 license that is
 * available at the URL https://creativecommons.org/licenses/by-sa/4.0/
 *
 * DISCLAIMER
 * This code is provided as is without any warranty.
 * No promise of being safe or secure
 *
 * @author   Sasan Rose <sasan.rose@gmail.com>
 * @author   Emiliano 'AlberT' Gabrielli <albert@faktiva.com>
 * @license  https://creativecommons.org/licenses/by-sa/4.0/  CC-BY-SA-4.0
 * @source   https://github.com/faktiva/php-redis-admin
 */

class Lists_Controller extends Controller
{
    public function addAction()
    {
        $added = false;

        if ($this->router->method == Router::POST) {
            $value = $this->inputs->post('value', null);
            $key = $this->inputs->post('key', null);
            $type = $this->inputs->post('type', null);
            $pivot = $this->inputs->post('pivot', null);

            if (isset($value) && trim($value) != '' && isset($key) && trim($key) != '' &&
                (isset($type) && in_array($type, array('before', 'after', 'prepend', 'append')))) {
                if (($type == 'before' || $type == 'after') && (!isset($pivot) || $pivot == '')) {
                    $added = false;
                } else {
                    switch ($type) {
                        case 'prepend':
                            $added = (bool) $this->db->lPush($key, $value);
                            break;
                        case 'append':
                            $added = (bool) $this->db->rPush($key, $value);
                            break;
                        case 'before':
                            $added = (bool) $this->db->lInsert($key, Redis::BEFORE, $pivot, $value);
                            break;
                        case 'after':
                            $added = (bool) $this->db->lInsert($key, Redis::AFTER, $pivot, $value);
                            break;
                    }
                }
            }
        }

        Template::factory('json')->render($added);
    }

    public function viewAction($key, $page = 0)
    {
        $count = $this->db->lSize(urldecode($key));
        $start = $page * 30;
        $values = $this->db->lRange(urldecode($key), $start, $start + 29);

        Template::factory()->render('lists/view', array('count' => $count, 'values' => $values, 'key' => urldecode($key),
                                                        'page' => $page, ));
    }

    public function delAction()
    {
        if ($this->router->method == Router::POST) {
            $key = $this->inputs->post('key');
            $value = $this->inputs->post('value');
            $type = $this->inputs->post('type_options');

            if ($type == 'all') {
                $this->db->lRem($key, $value, 0);
            } elseif ($type == 'count') {
                $count = $this->inputs->post('count');
                $this->db->lRem($key, $value, $count);
            }
        }

        $this->router->redirect("lists/view/{$this->app->current['serverId']}/{$this->app->current['database']}/".urlencode($key));
    }
}
