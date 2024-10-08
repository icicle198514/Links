<?php

class Links_Action extends Typecho_Widget implements Widget_Interface_Do
{
    private $db;
    private $options;
    private $prefix;

    public function insertLink()
    {
        if (Links_Plugin::form('insert')->validate()) {
            $this->response->goBack();
        }
        /** 取出数据 */
        $link = $this->request->from('email', 'image', 'url', 'state');

        /** 过滤XSS */
        $link['name'] = $this->request->filter('xss')->name;
		$link['city'] = $this->request->filter('xss')->city;
        $link['sort'] = $this->request->filter('xss')->sort;
        $link['description'] = $this->request->filter('xss')->description;
        $link['user'] = $this->request->filter('xss')->user;
        $link['order'] = $this->db->fetchObject($this->db->select(array('MAX(order)' => 'maxOrder'))->from($this->prefix . 'links'))->maxOrder + 1;

        /** 插入数据 */
        $link_lid = $this->db->query($this->db->insert($this->prefix . 'links')->rows($link));

        /** 设置高亮 */
        $this->widget('Widget_Notice')->highlight('link-' . $link_lid);

        /** 提示信息 */
        $this->widget('Widget_Notice')->set(_t(
            '友链 <a href="%s">%s</a> 已经被增加',
            $link['url'],
            $link['name']
        ), null, 'success');

        /** 转向原页 */
        $this->response->redirect(Typecho_Common::url('extending.php?panel=Links%2Fmanage-links.php', $this->options->adminUrl));
    }

    public function addMejituu()
    {
        /** 取出数据 */
        $link = array(
            'name' => "懵仙兔兔",
            'url' => "https://2dph.com",
            'email' => "acgm@qq.com",
            'image' => "https://2dph.com/logo.png",
            'description' => "永远相信，美好的事情即将发生——懵仙兔兔",
        );
        $link['order'] = $this->db->fetchObject($this->db->select(array('MAX(order)' => 'maxOrder'))->from($this->prefix . 'links'))->maxOrder + 1;

        /** 插入数据 */
        $link_lid = $this->db->query($this->db->insert($this->prefix . 'links')->rows($link));

        /** 设置高亮 */
        $this->widget('Widget_Notice')->highlight('link-' . $link_lid);

        /** 提示信息 */
        $this->widget('Widget_Notice')->set(_t(
            '友链 <a href="%s">%s</a> 已经被增加',
            $link['url'],
            $link['name']
        ), null, 'success');

        /** 转向原页 */
        $this->response->redirect(Typecho_Common::url('extending.php?panel=Links%2Fmanage-links.php', $this->options->adminUrl));
    }

    public function updateLink()
    {
        if (Links_Plugin::form('update')->validate()) {
            $this->response->goBack();
        }

        /** 取出数据 */
        $link = $this->request->from('email', 'image', 'url', 'state');
        $link_lid = $this->request->from('lid');

        /** 过滤XSS */
        $link['name'] = $this->request->filter('xss')->name;
        $link['sort'] = $this->request->filter('xss')->sort;
        $link['description'] = $this->request->filter('xss')->description;
        $link['user'] = $this->request->filter('xss')->user;

        /** 更新数据 */
        $this->db->query($this->db->update($this->prefix . 'links')->rows($link)->where('lid = ?', $link_lid));

        /** 设置高亮 */
        $this->widget('Widget_Notice')->highlight('link-' . $link_lid);

        /** 提示信息 */
        $this->widget('Widget_Notice')->set(_t(
            '友链 <a href="%s">%s</a> 已经被更新',
            $link['url'],
            $link['name']
        ), null, 'success');

        /** 转向原页 */
        $this->response->redirect(Typecho_Common::url('extending.php?panel=Links%2Fmanage-links.php', $this->options->adminUrl));
    }

    public function deleteLink()
    {
        $lids = $this->request->filter('int')->getArray('lid');
        $deleteCount = 0;
        if ($lids && is_array($lids)) {
            foreach ($lids as $lid) {
                if ($this->db->query($this->db->delete($this->prefix . 'links')->where('lid = ?', $lid))) {
                    $deleteCount++;
                }
            }
        }
        /** 提示信息 */
        $this->widget('Widget_Notice')->set(
            $deleteCount > 0 ? _t('友链已经删除') : _t('没有友链被删除'),
            null,
            $deleteCount > 0 ? 'success' : 'notice'
        );

        /** 转向原页 */
        $this->response->redirect(Typecho_Common::url('extending.php?panel=Links%2Fmanage-links.php', $this->options->adminUrl));
    }

    public function enableLink()
    {
        $lids = $this->request->filter('int')->getArray('lid');
        $enableCount = 0;
        if ($lids && is_array($lids)) {
            foreach ($lids as $lid) {
                if ($this->db->query($this->db->update($this->prefix . 'links')->rows(array('state' => '1'))->where('lid = ?', $lid))) {
                    $enableCount++;
                }
            }
        }
        /** 提示信息 */
        $this->widget('Widget_Notice')->set(
            $enableCount > 0 ? _t('友链已经启用') : _t('没有友链被启用'),
            null,
            $enableCount > 0 ? 'success' : 'notice'
        );

        /** 转向原页 */
        $this->response->redirect(Typecho_Common::url('extending.php?panel=Links%2Fmanage-links.php', $this->options->adminUrl));
    }

    public function prohibitLink()
    {
        $lids = $this->request->filter('int')->getArray('lid');
        $prohibitCount = 0;
        if ($lids && is_array($lids)) {
            foreach ($lids as $lid) {
                if ($this->db->query($this->db->update($this->prefix . 'links')->rows(array('state' => '0'))->where('lid = ?', $lid))) {
                    $prohibitCount++;
                }
            }
        }
        /** 提示信息 */
        $this->widget('Widget_Notice')->set(
            $prohibitCount > 0 ? _t('友链已经禁用') : _t('没有友链被禁用'),
            null,
            $prohibitCount > 0 ? 'success' : 'notice'
        );

        /** 转向原页 */
        $this->response->redirect(Typecho_Common::url('extending.php?panel=Links%2Fmanage-links.php', $this->options->adminUrl));
    }

    public function sortLink()
    {
        $links = $this->request->filter('int')->getArray('lid');
        if ($links && is_array($links)) {
            foreach ($links as $sort => $lid) {
                $this->db->query($this->db->update($this->prefix . 'links')->rows(array('order' => $sort + 1))->where('lid = ?', $lid));
            }
        }
    }

    public function emailLogo()
    {
        /* 邮箱头像解API接口 by 懵仙兔兔 */
        $type = $this->request->type;
        $email = $this->request->email;

        if ($email == null || $email == '') {
            $this->response->throwJson('请提交邮箱链接 [email=abc@abc.com]');
            exit;
        } else if ($type == null || $type == '' || ($type != 'txt' && $type != 'json')) {
            $this->response->throwJson('请提交type类型 [type=txt, type=json]');
            exit;
        } else {
            $f = str_replace('@qq.com', '', $email);
            $email = $f . '@qq.com';
            if (is_numeric($f) && strlen($f) < 11 && strlen($f) > 4) {
                stream_context_set_default([
                    'ssl' => [
                        'verify_host' => false,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ]);
                $geturl = 'https://s.p.qq.com/pub/get_face?img_type=3&uin=' . $f;
                $headers = get_headers($geturl, TRUE);
                if ($headers) {
                    $g = $headers['Location'];
                    $g = str_replace("http:", "https:", $g);
                } else {
                    $g = 'https://q.qlogo.cn/g?b=qq&nk=' . $f . '&s=100';
                }
            } else {
                $g = 'https://cdn.helingqi.com/wavatar/' . md5($email) . '?d=mm';
            }
            $r = array('url' => $g);
            if ($type == 'txt') {
                $this->response->throwJson($g);
                exit;
            } else if ($type == 'json') {
                $this->response->throwJson(json_encode($r));
                exit;
            }
        }
    }

    public function action()
    {
        Helper::security()->protect();
        $user = Typecho_Widget::widget('Widget_User');
        $user->pass('administrator');
        $this->db = Typecho_Db::get();
        $this->prefix = $this->db->getPrefix();
        $this->options = Typecho_Widget::widget('Widget_Options');
        $this->on($this->request->is('do=insert'))->insertLink();
        $this->on($this->request->is('do=addMejituu'))->addMejituu();
        $this->on($this->request->is('do=update'))->updateLink();
        $this->on($this->request->is('do=delete'))->deleteLink();
        $this->on($this->request->is('do=enable'))->enableLink();
        $this->on($this->request->is('do=prohibit'))->prohibitLink();
        $this->on($this->request->is('do=sort'))->sortLink();
        $this->on($this->request->is('do=email-logo'))->emailLogo();
        $this->response->redirect($this->options->adminUrl);
    }
}

/** Links by 懵仙兔兔 */
