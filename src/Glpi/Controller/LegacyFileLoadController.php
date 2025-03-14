<?php

/**
 * ---------------------------------------------------------------------
 *
 * GLPI - Gestionnaire Libre de Parc Informatique
 *
 * http://glpi-project.org
 *
 * @copyright 2015-2025 Teclib' and contributors.
 * @licence   https://www.gnu.org/licenses/gpl-3.0.html
 *
 * ---------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of GLPI.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * ---------------------------------------------------------------------
 */

namespace Glpi\Controller;

use Glpi\DependencyInjection\PublicService;
use Glpi\Http\HeaderlessStreamedResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LegacyFileLoadController implements PublicService
{
    public const REQUEST_FILE_KEY = '_glpi_file_to_load';

    private ?Request $request = null;

    public function __invoke(Request $request): Response
    {
        $this->request = $request;

        $target_file = $request->attributes->getString(self::REQUEST_FILE_KEY);

        if (!$target_file) {
            throw new \RuntimeException('Cannot load legacy controller without specifying a file to load.');
        }

        $callback = function () use ($target_file) {
            require $target_file;
        };

        return new HeaderlessStreamedResponse($callback->bindTo($this, self::class));
    }

    /**
     * Method used in `front/dropdown.common.form.php` to get the current request.
     *
     * @phpstan-ignore method.unused
     */
    private function getRequest(): Request
    {
        if ($this->request === null) {
            throw new \RuntimeException(\sprintf(
                'Could not find Request in "%s" controller. Did you forget to call "%s"?',
                self::class,
                '__invoke',
            ));
        }

        return $this->request;
    }
}
