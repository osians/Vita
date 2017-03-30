<?php

/* register.twig */
class __TwigTemplate_ed7fa7852b7e2b5e6e987ab787c19cbe552d3d64ad3219b2d0636a46938dd1c7 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("layout.twig", "register.twig", 1);
        $this->blocks = array(
            'body_class' => array($this, 'block_body_class'),
            'content' => array($this, 'block_content'),
            'scripts' => array($this, 'block_scripts'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "layout.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_body_class($context, array $blocks = array())
    {
        echo "login-page";
    }

    // line 5
    public function block_content($context, array $blocks = array())
    {
        // line 6
        echo "
<div class=\"register-box\">


    ";
        // line 11
        echo "    ";
        if ( !(null === $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "post", array()), "success", array()))) {
            // line 12
            echo "        ";
            // line 13
            echo "        ";
            if ((twig_length_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "post", array()), "success", array())) > 0)) {
                // line 14
                echo "            
            <div class=\"alert alert-success alert-dismissible\">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>
                <h4><i class=\"icon fa fa-check\"></i> Alert!</h4>
                Novo usuário adicionado com sucesso.
            </div>

        ";
            } else {
                // line 22
                echo "
            <div class=\"alert alert-danger alert-dismissible\">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>
                <h4><i class=\"icon fa fa-ban\"></i> Alert!</h4>
                Erro ao adicionar novo usuário: ";
                // line 26
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "post", array()), "error_message", array()), "html", null, true);
                echo "
            </div>

        ";
            }
            // line 30
            echo "    ";
        }
        // line 31
        echo "

    <div class=\"register-logo\">
        <a href=\"";
        // line 34
        echo twig_escape_filter($this->env, (isset($context["base_url"]) ? $context["base_url"] : null), "html", null, true);
        echo "\"><b>Controle</b> FINANCEIRO</a>
    </div>

    <div class=\"register-box-body\">
        <p class=\"login-box-msg\">Register a new membership</p>

        <form action=\"";
        // line 40
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "url", array()), "html", null, true);
        echo "Index/register\" method=\"post\">

            <input type=\"hidden\" name=\"formname\" value=\"usuarios\" readonly>
            <input type=\"hidden\" name=\"idusuario\" value=\"0\" readonly>

            <div class=\"form-group has-feedback\">
                <input type=\"text\" name=\"nome\" class=\"form-control\" placeholder=\"Nome\" required>
                <span class=\"glyphicon glyphicon-user form-control-feedback\"></span>
            </div>

            <div class=\"form-group has-feedback\">
                <input type=\"text\" name=\"sobrenome\" class=\"form-control\" placeholder=\"Sobrenome\" required>
                <span class=\"glyphicon glyphicon-user form-control-feedback\"></span>
            </div>

            <div class=\"form-group has-feedback\">
                <input type=\"email\" name=\"email\" class=\"form-control\" placeholder=\"Email\" required>
                <span class=\"glyphicon glyphicon-envelope form-control-feedback\"></span>
            </div>

            <div class=\"form-group has-feedback\">
                <input type=\"password\" name=\"senha\" class=\"form-control\" placeholder=\"Password\" required>
                <span class=\"glyphicon glyphicon-lock form-control-feedback\"></span>
            </div>

            <div class=\"form-group has-feedback\">
                <input type=\"password\" name=\"senha|compare\" class=\"form-control\" placeholder=\"Retype password\" required>
                <span class=\"glyphicon glyphicon-log-in form-control-feedback\"></span>
            </div>

            <div class=\"row\">
                <div class=\"col-xs-8\">
                    <div class=\"checkbox icheck\">
                        <label>
                            <input type=\"checkbox\"> I agree to the <a href=\"#\">terms</a>
                        </label>
                    </div>
                </div>
                <!-- /.col -->
                <div class=\"col-xs-4\">
                    <button type=\"submit\" class=\"btn btn-primary btn-block btn-flat\">Register</button>
                </div>
                <!-- /.col -->
            </div>
        </form>

        <div class=\"social-auth-links text-center\">
            <p>- OR -</p>
            <a href=\"#\" class=\"btn btn-block btn-social btn-facebook btn-flat\"><i class=\"fa fa-facebook\"></i> Sign up using Facebook</a>
            <a href=\"#\" class=\"btn btn-block btn-social btn-google btn-flat\"><i class=\"fa fa-google-plus\"></i> Sign up using Google+</a>
        </div>

        <a href=\"";
        // line 92
        echo twig_escape_filter($this->env, (isset($context["base_url"]) ? $context["base_url"] : null), "html", null, true);
        echo "\" class=\"text-center\">I already have a membership</a>
    </div>
    <!-- /.form-box -->
</div>
<!-- /.register-box -->

";
    }

    // line 100
    public function block_scripts($context, array $blocks = array())
    {
        // line 101
        echo "    <!-- iCheck -->
    <script src=\"";
        // line 102
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "plugins/iCheck/icheck.min.js\"></script>
    <script>
      \$(function () {
        \$('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
      });
    </script>
";
    }

    public function getTemplateName()
    {
        return "register.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  165 => 102,  162 => 101,  159 => 100,  148 => 92,  93 => 40,  84 => 34,  79 => 31,  76 => 30,  69 => 26,  63 => 22,  53 => 14,  50 => 13,  48 => 12,  45 => 11,  39 => 6,  36 => 5,  30 => 3,  11 => 1,);
    }
}
/* {% extends 'layout.twig' %}*/
/* */
/* {% block body_class %}login-page{% endblock %}*/
/* */
/* {% block content %}*/
/* */
/* <div class="register-box">*/
/* */
/* */
/*     {# se a variavel esta definida #}*/
/*     {% if vita.post.success is not null %}*/
/*         {# se o resultado nao for falso #}*/
/*         {% if vita.post.success|length > 0 %}*/
/*             */
/*             <div class="alert alert-success alert-dismissible">*/
/*                 <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>*/
/*                 <h4><i class="icon fa fa-check"></i> Alert!</h4>*/
/*                 Novo usuário adicionado com sucesso.*/
/*             </div>*/
/* */
/*         {% else %}*/
/* */
/*             <div class="alert alert-danger alert-dismissible">*/
/*                 <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>*/
/*                 <h4><i class="icon fa fa-ban"></i> Alert!</h4>*/
/*                 Erro ao adicionar novo usuário: {{ vita.post.error_message }}*/
/*             </div>*/
/* */
/*         {% endif %}*/
/*     {% endif %}*/
/* */
/* */
/*     <div class="register-logo">*/
/*         <a href="{{ base_url }}"><b>Controle</b> FINANCEIRO</a>*/
/*     </div>*/
/* */
/*     <div class="register-box-body">*/
/*         <p class="login-box-msg">Register a new membership</p>*/
/* */
/*         <form action="{{ vita.config.url }}Index/register" method="post">*/
/* */
/*             <input type="hidden" name="formname" value="usuarios" readonly>*/
/*             <input type="hidden" name="idusuario" value="0" readonly>*/
/* */
/*             <div class="form-group has-feedback">*/
/*                 <input type="text" name="nome" class="form-control" placeholder="Nome" required>*/
/*                 <span class="glyphicon glyphicon-user form-control-feedback"></span>*/
/*             </div>*/
/* */
/*             <div class="form-group has-feedback">*/
/*                 <input type="text" name="sobrenome" class="form-control" placeholder="Sobrenome" required>*/
/*                 <span class="glyphicon glyphicon-user form-control-feedback"></span>*/
/*             </div>*/
/* */
/*             <div class="form-group has-feedback">*/
/*                 <input type="email" name="email" class="form-control" placeholder="Email" required>*/
/*                 <span class="glyphicon glyphicon-envelope form-control-feedback"></span>*/
/*             </div>*/
/* */
/*             <div class="form-group has-feedback">*/
/*                 <input type="password" name="senha" class="form-control" placeholder="Password" required>*/
/*                 <span class="glyphicon glyphicon-lock form-control-feedback"></span>*/
/*             </div>*/
/* */
/*             <div class="form-group has-feedback">*/
/*                 <input type="password" name="senha|compare" class="form-control" placeholder="Retype password" required>*/
/*                 <span class="glyphicon glyphicon-log-in form-control-feedback"></span>*/
/*             </div>*/
/* */
/*             <div class="row">*/
/*                 <div class="col-xs-8">*/
/*                     <div class="checkbox icheck">*/
/*                         <label>*/
/*                             <input type="checkbox"> I agree to the <a href="#">terms</a>*/
/*                         </label>*/
/*                     </div>*/
/*                 </div>*/
/*                 <!-- /.col -->*/
/*                 <div class="col-xs-4">*/
/*                     <button type="submit" class="btn btn-primary btn-block btn-flat">Register</button>*/
/*                 </div>*/
/*                 <!-- /.col -->*/
/*             </div>*/
/*         </form>*/
/* */
/*         <div class="social-auth-links text-center">*/
/*             <p>- OR -</p>*/
/*             <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign up using Facebook</a>*/
/*             <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign up using Google+</a>*/
/*         </div>*/
/* */
/*         <a href="{{ base_url }}" class="text-center">I already have a membership</a>*/
/*     </div>*/
/*     <!-- /.form-box -->*/
/* </div>*/
/* <!-- /.register-box -->*/
/* */
/* {% endblock %}*/
/* */
/* {% block scripts %}*/
/*     <!-- iCheck -->*/
/*     <script src="{{ vita.config.template_url }}plugins/iCheck/icheck.min.js"></script>*/
/*     <script>*/
/*       $(function () {*/
/*         $('input').iCheck({*/
/*             checkboxClass: 'icheckbox_square-blue',*/
/*             radioClass: 'iradio_square-blue',*/
/*             increaseArea: '20%' // optional*/
/*         });*/
/*       });*/
/*     </script>*/
/* {% endblock %}*/
