<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Application;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test the module controller
 *
 * @covers \Fisharebest\Webtrees\Http\Controllers\ModuleController
 */
class ModuleControllerTest extends \Fisharebest\Webtrees\TestCase
{
    protected static $uses_database = true;

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function testMissingModule(): void
    {
        app()->instance(Tree::class, Tree::create('name', 'title'));
        app()->instance(Request::class, new Request(['route' => 'module']));

        $controller = app()->make(ModuleController::class);
        app()->dispatch($controller, 'action');
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function testInvalidModule(): void
    {
        $tree = Tree::create('name', 'title');
        app()->instance(Tree::class, $tree);

        $request    = new Request(['route' => 'module', 'module' => 'no-such-module']);
        app()->instance(Request::class, $request);

        $controller = app()->make(ModuleController::class);
        app()->dispatch($controller, 'action');
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function testMissingAction(): void
    {
        $tree = Tree::create('name', 'title');
        app()->instance(Tree::class, $tree);

        $request    = new Request(['route' => 'module', 'module' => 'sitemap']);
        app()->instance(Request::class, $request);

        $controller = app()->make(ModuleController::class);
        app()->dispatch($controller, 'action');
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function testInvalidAction(): void
    {
        $tree = Tree::create('name', 'title');
        app()->instance(Tree::class, $tree);

        $request    = new Request(['route' => 'module', 'module' => 'sitemap', 'action' => 'no-such-action']);
        app()->instance(Request::class, $request);

        $controller = app()->make(ModuleController::class);
        app()->dispatch($controller, 'action');
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     * @return void
     */
    public function testVisitorCannotUseAdminAction(): void
    {
        $tree = Tree::create('name', 'title');
        app()->instance(Tree::class, $tree);

        $request = new Request(['route' => 'module', 'module' => 'sitemap', 'action' => 'DoAdminStuff']);
        app()->instance(Request::class, $request);

        $controller = app()->make(ModuleController::class);
        app()->dispatch($controller, 'action');
    }

    /**
     * @return void
     */
    public function testSucessfulAction(): void
    {
        $tree = Tree::create('name', 'title');
        app()->instance(Tree::class, $tree);

        $request = new Request(['route' => 'module', 'module' => 'sitemap', 'action' => 'Index']);
        app()->instance(Request::class, $request);

        $controller = app()->make(ModuleController::class);
        $response = app()->dispatch($controller, 'action');

        $this->assertInstanceOf(Response::class, $response);
    }
}
