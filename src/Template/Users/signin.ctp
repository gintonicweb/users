<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Signup'), ['action' => 'signup']) ?></li>
        <li><?= $this->Html->link(__('Recover Password'), ['action' => 'sendRecovery']) ?></li>
    </ul>
</div>
<div class="users form large-10 medium-9 columns">
    <?= $this->Form->create('Users') ?>
    <fieldset>
        <legend><?= __('Signin') ?></legend>
        <?php
            echo $this->Form->input('email');
            echo $this->Form->input('password');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Signin')) ?>
    <?= $this->Form->end() ?>
</div>
