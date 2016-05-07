<?php

use Cake\Core\Plugin;

Plugin::load('ADmad/JwtAuth');
Plugin::load('CrudUsers');
Plugin::load('Muffin/Tokenize', ['bootstrap' => true, 'routes' => true]);
Plugin::load('Muffin/Trash');
Plugin::load('Xety/Cake3CookieAuth');
