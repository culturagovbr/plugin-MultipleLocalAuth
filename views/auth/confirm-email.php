<?php
/**
 * @var \MapasCulturais\Themes\BaseV2\Theme $this
 * @var \MapasCulturais\App $app
 * 
 */

use MapasCulturais\App;
use MapasCulturais\i;

$this->import('
    mc-card
');

$app = App::i();
$redirectTo = $app->auth->isUserAuthenticated() ? $app->createUrl('site','index') : $app->createUrl('auth','?redirectTo=');
?>

<div class="confirm-email">
    <mc-card class="no-title">
        <template #content>
            <div class="grid-12">
                <div class="col-12 header">
                    <label class="header__title"> <?= i::__('Sua conta foi criada com sucesso!') ?> </label>
                    <mc-icon name="circle-checked" class="header__icon"></mc-icon>
                    <label class="header__label"> <?= i::__('') ?> </label>
                </div>
            </div>
        </template>
    </mc-card>
</div>
<script>
    setTimeout(() => window.location.href = '<?= $redirectTo ?>', 4000);
</script>