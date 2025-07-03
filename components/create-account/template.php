<?php
/**
 * @var \MapasCulturais\Themes\BaseV2\Theme $this
 * @var \MapasCulturais\App $app
 * 
 */

use MapasCulturais\i;

$this->import('
    entity-field
    entity-terms
    mc-card
    mc-icon
    mc-stepper
    password-strongness
    mc-captcha
');
?>

<div class="create-account"> 

    <div v-if="!created" class="create-account__title">
        <label><?= $this->text('title', i::__('Nova conta')) ?> </label>
        <p><?= $this->text('description', sprintf(i::__('Siga os passos para criar sua conta no %s.'), $app->siteName)) ?> </p>
    </div>

    <!-- Creating account -->
    <mc-card v-if="!created" class="no-title">        
        <template #content> 
            <div class="create-account__timeline">
            <mc-stepper :steps="arraySteps" :step="actualStep" @stepChanged="goToStep" disable-navigation :no-labels="true" :count-class="'custom-count'" :showStepNumber="true"></mc-stepper>
            </div>

            <!-- First step -->
            <div v-if="step==0" class="create-account__step grid-12">
                <form class="col-12 grid-12" @submit.prevent="nextStep();">
                    <div class="field col-12">
                        <label for="email"> <?= i::__('E-mail') ?> </label>
                        <input type="text" name="email" id="email" v-model="email" />
                    </div>
                    <div class="field col-12">
                        <label class="document-label" for="cpf"> 
                            <?= i::__('CPF') ?> 
                            <div class="question">
                                <VMenu class="popover">
                                    <button tabindex="-1" class="question" type="button"> <?= i::__('Por que pedimos este dado') ?> <mc-icon name="question"></mc-icon> </button>
                                    <template #popper>
                                        <?= i::__('Texto sobre o motivo da coleta do CPF') ?>
                                    </template>
                                </VMenu>
                            </div>
                        </label>
                        <input type="text" name="cpf" id="cpf" v-model="cpf" v-maska data-maska="###.###.###-##" maxlength="14" /> 
                    </div>
                    <div class="field col-12 password">
                        <label for="pwd"> <?= i::__('Senha'); ?> </label>
                        <input autocomplete="off" id="pwd" type="password" name="password" v-model="password" />
                        <div class="seePassword" @click="togglePassword('pwd', $event)"></div>
                    </div>
                    <div class="field col-12 password">
                        <label for="pwd-check">
                            <?= i::__('Confirme sua senha'); ?>
                        </label>
                        <input autocomplete="off" id="pwd-check" type="password" name="confirm_password" v-model="confirmPassword" />
                        <div class="seePassword" @click="togglePassword('pwd-check', $event)"></div>
                    </div>
                    <div class="col-12">
                        <password-strongness :password="password"></password-strongness>
                    </div>
                
                    <mc-captcha @captcha-verified="verifyCaptcha" @captcha-expired="expiredCaptcha" :error="error" class="col-12"></mc-captcha>
                    
                    <button class="col-12 button button--primary button--large button--md" type="submit"> <?= i::__('Continuar') ?> </button>
                </form>
                
                <div v-if="configs.strategies.Google?.visible || configs.strategies.govbr?.visible" class="divider col-12"></div>

                <div v-if="configs.strategies.Google?.visible || configs.strategies.govbr?.visible" class="social-login col-12">
                    <a v-if="configs.strategies.govbr?.visible" class="social-login--button button button--icon button--large button--md govbr" href="<?php echo $app->createUrl('auth', 'govbr') ?>">                                
                        <div class="img"> <img height="16" class="br-sign-in-img" src="<?php $this->asset('img/govbr-white.png'); ?>" /> </div>                                
                        <?= i::__('Entrar com Gov.br') ?>                            
                    </a>                    
                    <a v-if="configs.strategies.Google?.visible" class="social-login--button button button--icon button--large button--md google" href="<?php echo $app->createUrl('auth', 'google') ?>">                                
                        <div class="img"> <img height="16" src="<?php $this->asset('img/g.png'); ?>" /> </div>                                
                        <?= i::__('Entrar com Google') ?>
                    </a>
                </div>
            </div>

            <!-- Terms steps -->
            <div v-show="step==index+1" v-for="(value, name, index) in terms" class="create-account__step grid-12">
                <label class="title col-12"> {{value.title}} </label>
                <div class="term col-12" v-html="value.text" :id="'term'+index" ref="terms"></div>
                <div class="divider col-12"></div>                
                <button class="col-12 button button--primary button--large button--md" :id="'acceptTerm'+index" @click="nextStep(); acceptTerm(name)"> {{value.buttonText}} </button>
                <button class="col-12 button button--text" @click="cancel()"> <?= i::__('Voltar e excluir minhas informações') ?> </button>
            </div>

            <!-- Last step -->
            <div v-if="step==totalSteps-1" class="create-account__step grid-12">
                <label class="title col-12">
                    <div class="subtitle col-12">
                        <span> <?= i::__('Falta pouco para ter sua conta criada!') ?> </span>
                        <span> <?= i::__('Preencha seu nome e uma breve descrição sua.') ?> </span>
                        <span> <?= i::__('As pessoas encontrarão você por esse nome.') ?> </span>
                    </div>
                </label>
                
                <entity-field :entity="agent" classes="col-12" hide-required label=<?php i::esc_attr_e("Nome")?> prop="name" fieldDescription="<?= i::__('As pessoas irão encontrar você por esse nome.') ?>"></entity-field>
                <entity-field :entity="agent" classes="col-12" hide-required prop="shortDescription" label="<?php i::esc_attr_e("Mini Bio")?>"></entity-field>
                <entity-terms :entity="agent" classes="col-12" :editable="true" taxonomy='area' title="<?php i::esc_attr_e("Área de atuação") ?>"></entity-terms>                

                <mc-captcha @captcha-verified="verifyCaptcha" @captcha-expired="expiredCaptcha" :error="error" class="col-12"></mc-captcha>
                <button class="col-12 button button--primary button--large button--md" @click="register()"> <?= i::__('Criar conta') ?></button>
            </div>
        </template>
    </mc-card>
    
    <!-- Account created -->            
    <mc-card v-if="created" class="no-title card-created">
        <template #content>
            <div class="create-account__created grid-12">
                <div class="col-12 title">
                    <mc-icon name="circle-checked" class="title__icon"></mc-icon>
                    <label v-if="emailSent" class="col-12 title__label"> <?= i::__('Sua conta está sendo criada! <br /> Vá ao seu e-mail e valide a sua conta!') ?> </label>
                    <label v-if="!emailSent" class="col-12 title__label"> <?= i::__('Sua conta foi criada com sucesso!') ?> </label>
                </div>

                <p v-if="emailSent" class="emailSent col-12"> <?= sprintf($this->text('email-sent', i::__('Acesse seu e-mail para confirmar a criação de sua conta no %s.')), $app->siteName) ?> </p>
            </div>
        </template>
    </mc-card>
</div>
