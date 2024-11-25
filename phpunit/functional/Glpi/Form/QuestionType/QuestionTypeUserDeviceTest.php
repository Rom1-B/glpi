<?php

/**
 * ---------------------------------------------------------------------
 *
 * GLPI - Gestionnaire Libre de Parc Informatique
 *
 * http://glpi-project.org
 *
 * @copyright 2015-2024 Teclib' and contributors.
 * @copyright 2003-2014 by the INDEPNET Development Team.
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

namespace tests\units\Glpi\Form\QuestionType;

use Computer;
use DbTestCase;
use Glpi\Form\Destination\FormDestinationTicket;
use Glpi\Form\QuestionType\QuestionTypeUserDevice;
use Glpi\Tests\FormBuilder;
use Glpi\Tests\FormTesterTrait;
use Session;

final class QuestionTypeUserDeviceTest extends DbTestCase
{
    use FormTesterTrait;

    public function testUserDeviceAnswerIsDisplayedInTicketDescription(): void
    {
        $this->login();
        $computer = $this->createItem(Computer::class, [
            'name' => 'My computer',
            'entities_id' => Session::getActiveEntity(),
            'users_id' => Session::getLoginUserID(),
        ]);

        $builder = new FormBuilder();
        $builder->addQuestion("Computer", QuestionTypeUserDevice::class);
        $builder->addDestination(FormDestinationTicket::class, "My ticket");
        $form = $this->createForm($builder);

        $ticket = $this->sendFormAndGetCreatedTicket($form, [
            "Computer" => 'Computer_' . $computer->getID(),
        ]);

        $this->assertStringContainsString(
            "1) Computer: My computer",
            strip_tags($ticket->fields['content']),
        );
    }
}