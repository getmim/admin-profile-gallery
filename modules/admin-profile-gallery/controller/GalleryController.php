<?php
/**
 * GalleryController
 * @package admin-profile-gallery
 * @version 0.0.1
 */

namespace AdminProfileGallery\Controller;

use ProfileGallery\Model\ProfileGallery as PGallery;
use LibFormatter\Library\Formatter;
use LibForm\Library\Form;
use LibForm\Library\Combiner;
use LibPagination\Library\Paginator;
use Profile\Model\Profile;

class GalleryController extends \Admin\Controller
{
	private function getParams(string $title): array{
        return [
            '_meta' => [
                'title' => $title,
                'menus' => ['profile']
            ],
            'subtitle' => $title,
            'pages' => null
        ];
    }

    public function editAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_profile)
            return $this->show404();

        $prof_id = $this->req->param->profile;
        $profile = Profile::getOne(['id'=>$prof_id]);
        if(!$profile)
            return $this->show404();

        $gallery = (object)[];

        $id = $this->req->param->id;
        if($id){
            $gallery = PGallery::getOne(['id'=>$id,'profile'=>$profile->id]);
            if(!$gallery)
                return $this->show404();
            $params = $this->getParams('Edit Profile Gallery');
        }else{
            $params = $this->getParams('Create New Profile Gallery');
        }

        $params['profile'] = $profile;
        $form              = new Form('admin.profile-gallery.edit');
        $params['form']    = $form;
        $params['active_menu'] = 'adminProfileGallery';

        if(!($valid = $form->validate($gallery))|| !$form->csrfTest('noob'))
            return $this->resp('profile/gallery/edit', $params);

        if($id){
            if(!PGallery::set((array)$valid, ['id'=>$id]))
                deb(PGallery::lastError());
        }else{
            $valid->user    = $this->user->id;
            $valid->profile = $profile->id;
            if(!PGallery::create((array)$valid))
                deb(PGallery::lastError());
        }

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => 0,
            'method' => $id ? 2 : 1,
            'type'   => 'profile-gallery',
            'original' => $gallery,
            'changes'  => $valid
        ]);

        $next = $this->router->to('adminProfileGallery', ['id'=>$profile->id]);
        $this->res->redirect($next);
    }

    public function indexAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_profile)
            return $this->show404();

        $params = $this->getParams('Edit Profile Gallery');

        $id = $this->req->param->id;
        $profile = Profile::getOne(['id'=>$id]);
        if(!$profile)
            return $this->show404();

        $params['profile'] = $profile;

        list($page, $rpp) = $this->req->getPager(25, 50);

        $cond = ['profile'=>$profile->id];
        $pcond = [];
        if($q = $this->req->getQuery('q'))
            $pcond['q'] = $cond['q'] = $q;

        $galleries = PGallery::get($cond, $rpp, $page, ['created'=>false]) ?? [];
        if($galleries)
            $galleries = Formatter::formatMany('profile-gallery', $galleries, ['user','profile']);
        $params['galleries'] = $galleries;

        $params['total'] = $total = PGallery::count($cond);
        if($total > $rpp){
            $params['pages'] = new Paginator(
                $this->router->to('adminProfile'),
                $total,
                $page,
                $rpp,
                10,
                $pcond
            );
        }

        $params['form'] = new Form('admin.profile-gallery.index');
        $params['form']->validate( (object)$this->req->get() );

        $this->resp('profile/gallery/index', $params);
    }

    public function removeAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_profile)
            return $this->show404();

        $id     = $this->req->param->id;
        $prof_id= $this->req->param->profile;
        $profile  = Profile::getOne(['id'=>$prof_id]);
        if(!$profile)
            return $this->show404();
        $next   = $this->router->to('adminProfileGallery', ['id'=>$profile->id]);
        $form   = new Form('admin.profile-gallery.index');

        $gallery = PGallery::getOne(['id'=>$id,'profile'=>$profile->id]);
        if(!$gallery)
            return $this->show404();

        if(!$form->csrfTest('noob'))
            return $this->res->redirect($next);

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => 0,
            'method' => 3,
            'type'   => 'profile-gallery',
            'original' => $profile,
            'changes'  => null
        ]);

        PGallery::remove(['id'=>$id]);

        $this->res->redirect($next);
    }
}