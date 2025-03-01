<?php /** @var \Kirby\Cms\Block $block */ ?>

<?php $layout = $block->layout()->toLayouts()->first() ?>

<?php if ($layout !== null): ?>

  <section class="grid" id="<?= $layout->id() ?>">

    <?php foreach ($layout->columns() as $column): ?>

      <div class="column" style="--columns:<?= $column->span() ?>">

        <div class="blocks">

          <?= $column->blocks() ?>

        </div>
        
      </div>
      
    <?php endforeach ?>

  </section>

<?php endif ?>