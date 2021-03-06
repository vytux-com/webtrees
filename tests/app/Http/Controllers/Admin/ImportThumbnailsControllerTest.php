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

namespace Fisharebest\Webtrees\Http\Controllers\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test ImportThumbnailsController class.
 *
 * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\ImportThumbnailsController
 */
class ImportThumbnailsControllerTest extends \Fisharebest\Webtrees\TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testWebtrees1Thumbnails(): void
    {
        $controller = new ImportThumbnailsController();
        $response   = $controller->webtrees1Thumbnails();

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testWebtrees1ThumbnailsAction(): void
    {
        $controller = new ImportThumbnailsController();
        $response   = $controller->webtrees1ThumbnailsAction(new Request());

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testWebtrees1ThumbnailsData(): void
    {
        $controller = new ImportThumbnailsController();
        $response   = $controller->webtrees1ThumbnailsData(new Request());

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }
}
