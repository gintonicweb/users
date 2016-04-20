<?php

use Cake\Core\Plugin;

Plugin::load('CrudUsers');
Plugin::load('FOC/Authenticate');
Plugin::load('ADmad/JwtAuth');
Plugin::load('Muffin/Tokenize', ['bootstrap' => true, 'routes' => true]);
Plugin::load('Muffin/Trash');
