<?php

namespace Bolt\Extension\Bolt\DiyForms;

use Bolt\Application;
use Bolt\BaseExtension;
// use Mahango\Form\Type\ContactType;
use Bolt\Extension\Bolt\DiyForms\Form\Type\ContactType;
use Symfony\Component\HttpFoundation\Request;

/**
 * DiyForms an example bolt extension demonstraiting how to bind 
 * a form to a route and submit it's data to a contenttype.
 *
 * Copyright (C) 2015 Matthew Vickery
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    Matthew Vickery <vickery.matthew@gmail.com>
 * @copyright Copyright (c) 2015, Matthew Vickery
 * @license   http://opensource.org/licenses/GPL-3.0 GNU Public License 3.0
 */
class Extension extends BaseExtension
{
    /**
     * Extension name
     *
     * @var string
     */
    const NAME = "DiyForms";

    public function getName()
    {
        return Extension::NAME;
    }

    public function initialize() {
        // when the route /contact is requested by either a GET or a POST request 
        // the showForm() method will be called
        $this->app->match("/contact", array($this, 'showForm'))
            ->bind('show_form')
            ->method('GET|POST');
    }

    public function showForm(Request $request, $errors = null)
    {
        // create a symfony forms object
        $form = $this->app['form.factory']->create(new ContactType(), array());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            // do something with the data
            // here I'm saving it into the contact contenttype 
            // using the method defined further down the page
            if ($this->writeToContentype('contacts', $data)) {

                // TODO: send notification emails to user and admin 

                // save an alert message in the session
                // this could be displayed on the next page, perhaps using a Foundation alert-box
                $this->app['session']->getFlashBag()->set('success', 'Your enquiry has been received. Thank you.');
            } else {

                // TODO: send error notification email

                // alert users of an error
                $this->app['session']->getFlashBag()->set('error', 'There was an error saving your enquiry.');
            }

            // redirect to the homepage
            // it's always a good idea to redirect after a form submission 
            // so a form cannot be submitted multiple times by simply refreshing the page
            return $this->app->redirect($this->app["url_generator"]->generate("homepage"));
        }

        return $this->render('contact.twig', array('form' => $form->createView()));
    }

    private function render($template, $data)
    {
        $this->app['twig.loader.filesystem']->addPath(dirname(__FILE__) . '/templates');

        return $this->app['render']->render($template, $data);
    }

    /**
     * Write out form data to a specified contenttype table
     *
     * Method copied form https://github.com/GawainLynch/bolt-extension-boltforms
     *
     * @param string $contenttype
     * @param array  $data
     */
    public function writeToContentype($contenttype, array $data)
    {
        // Get an empty record for out contenttype
        $record = $this->app['storage']->getEmptyContent($contenttype);

        foreach ($data as $key => $value) {
            // Symfony makes empty fields NULL, PostgreSQL gets mad.
            if (is_null($value)) {
                $data[$key] = '';
            }
            // JSON encode arrays
            if (is_array($value)) {
                $data[$key] = json_encode($value);
            }
        }
        // Set a published date
        if (empty($data['datepublish'])) {
            $data['datepublish'] = date('Y-m-d H:i:s');
        }
        // Store the data array into the record
        $record->setValues($data);
        
        // Return the ID of the record
        return $this->app['storage']->saveContent($record);
    }
}






