<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Signin'), ['action' => 'signin']) ?></li>
        <li><?= $this->Html->link(__('Signup'), ['action' => 'signup']) ?></li>
    </ul>
</div>
<div class="users form large-10 medium-9 columns">
    <?= $this->Form->create('Users') ?>
    <fieldset>
        <legend><?= __('Recover Password') ?></legend>
        <?= $this->Form->input('email') ?>
    </fieldset>
    <?= $this->Form->button(__('Signin')) ?>
    <?= $this->Form->end() ?>
</div>
