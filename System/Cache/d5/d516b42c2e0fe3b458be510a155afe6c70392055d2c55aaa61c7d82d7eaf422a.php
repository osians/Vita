<?php

/* login.twig */
class __TwigTemplate_9763031be87797b055950f853c92f7243113a74cc815995f6f7f9fc25a05150f extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("layout.twig", "login.twig", 1);
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
    <div class=\"login-box\">

        ";
        // line 10
        echo "        ";
        if (($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "get", array(0 => "login_result"), "method", true, true) && (twig_length_filter($this->env, $this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "get", array(0 => "login_message"), "method")) > 0))) {
            // line 11
            echo "
            <div class=\"alert alert-danger alert-dismissible\">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>
                <h4><i class=\"icon fa fa-ban\"></i> Oops!</h4>
                ";
            // line 15
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "get", array(0 => "login_message"), "method"), "html", null, true);
            echo "
            </div>

        ";
        }
        // line 19
        echo "
        <div class=\"login-logo\">
            <a href=\"";
        // line 21
        echo twig_escape_filter($this->env, (isset($context["base_url"]) ? $context["base_url"] : null), "html", null, true);
        echo "\"><b>Controle</b> FINANCEIRO</a>
        </div>
        <!-- /.login-logo -->
        <div class=\"login-box-body\">
            <p class=\"login-box-msg\">Sign in to start your session</p>

            <form action=\"";
        // line 27
        echo twig_escape_filter($this->env, (isset($context["base_url"]) ? $context["base_url"] : null), "html", null, true);
        echo "Index/login\" method=\"post\">
                <div class=\"form-group has-feedback\">
                    <input type=\"email\" class=\"form-control\" name=\"email\" placeholder=\"Email\" required>
                    <span class=\"glyphicon glyphicon-envelope form-control-feedback\"></span>
                </div>
                <div class=\"form-group has-feedback\">
                    <input type=\"password\" class=\"form-control\" name=\"senha\" placeholder=\"Password\" required>
                    <span class=\"glyphicon glyphicon-lock form-control-feedback\"></span>
                </div>
                <div class=\"row\">
                    <div class=\"col-xs-8\">
                        <div class=\"checkbox icheck\">
                            <label>
                                <input type=\"checkbox\"> Remember Me
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class=\"col-xs-4\">
                        <button type=\"submit\" class=\"btn btn-primary btn-block btn-flat\">Sign In</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            <div class=\"social-auth-links text-center\">
                <p>- OR -</p>
                <a href=\"#\" class=\"btn btn-block btn-social btn-facebook btn-flat\"><i class=\"fa fa-facebook\"></i> Sign in using Facebook</a>
                <a href=\"#\" class=\"btn btn-block btn-social btn-google btn-flat\"><i class=\"fa fa-google-plus\"></i> Sign in using Google+</a>
            </div>
            <!-- /.social-auth-links -->
            <a href=\"#\">I forgot my password</a><br>
            <a href=\"";
        // line 59
        echo twig_escape_filter($this->env, (isset($context["base_url"]) ? $context["base_url"] : null), "html", null, true);
        echo "Index/register\" class=\"text-center\">Register a new membership</a>
        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->

";
    }

    // line 67
    public function block_scripts($context, array $blocks = array())
    {
        // line 68
        echo "    <!-- iCheck -->
    <script src=\"";
        // line 69
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
        return "login.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  125 => 69,  122 => 68,  119 => 67,  108 => 59,  73 => 27,  64 => 21,  60 => 19,  53 => 15,  47 => 11,  44 => 10,  39 => 6,  36 => 5,  30 => 3,  11 => 1,);
    }
}
/* {% extends 'layout.twig' %}*/
/* */
/* {% block body_class %}login-page{% endblock %}*/
/* */
/* {% block content %}*/
/* */
/*     <div class="login-box">*/
/* */
/*         {# se o resultado nao for falso #}*/
/*         {% if vita.get('login_result') is defined and vita.get('login_message')|length > 0 %}*/
/* */
/*             <div class="alert alert-danger alert-dismissible">*/
/*                 <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>*/
/*                 <h4><i class="icon fa fa-ban"></i> Oops!</h4>*/
/*                 {{ vita.get('login_message') }}*/
/*             </div>*/
/* */
/*         {% endif %}*/
/* */
/*         <div class="login-logo">*/
/*             <a href="{{ base_url }}"><b>Controle</b> FINANCEIRO</a>*/
/*         </div>*/
/*         <!-- /.login-logo -->*/
/*         <div class="login-box-body">*/
/*             <p class="login-box-msg">Sign in to start your session</p>*/
/* */
/*             <form action="{{ base_url }}Index/login" method="post">*/
/*                 <div class="form-group has-feedback">*/
/*                     <input type="email" class="form-control" name="email" placeholder="Email" required>*/
/*                     <span class="glyphicon glyphicon-envelope form-control-feedback"></span>*/
/*                 </div>*/
/*                 <div class="form-group has-feedback">*/
/*                     <input type="password" class="form-control" name="senha" placeholder="Password" required>*/
/*                     <span class="glyphicon glyphicon-lock form-control-feedback"></span>*/
/*                 </div>*/
/*                 <div class="row">*/
/*                     <div class="col-xs-8">*/
/*                         <div class="checkbox icheck">*/
/*                             <label>*/
/*                                 <input type="checkbox"> Remember Me*/
/*                             </label>*/
/*                         </div>*/
/*                     </div>*/
/*                     <!-- /.col -->*/
/*                     <div class="col-xs-4">*/
/*                         <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>*/
/*                     </div>*/
/*                     <!-- /.col -->*/
/*                 </div>*/
/*             </form>*/
/* */
/*             <div class="social-auth-links text-center">*/
/*                 <p>- OR -</p>*/
/*                 <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using Facebook</a>*/
/*                 <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using Google+</a>*/
/*             </div>*/
/*             <!-- /.social-auth-links -->*/
/*             <a href="#">I forgot my password</a><br>*/
/*             <a href="{{ base_url }}Index/register" class="text-center">Register a new membership</a>*/
/*         </div>*/
/*         <!-- /.login-box-body -->*/
/*     </div>*/
/*     <!-- /.login-box -->*/
/* */
/* {% endblock %}*/
/* */
/* {% block scripts %}*/
/*     <!-- iCheck -->*/
/*     <script src="{{ vita.config.template_url }}plugins/iCheck/icheck.min.js"></script>*/
/*     <script>*/
/*       $(function () {*/
/*         $('input').iCheck({*/
/*           checkboxClass: 'icheckbox_square-blue',*/
/*           radioClass: 'iradio_square-blue',*/
/*           increaseArea: '20%' // optional*/
/*         });*/
/*       });*/
/*     </script>*/
/* {% endblock %}*/
