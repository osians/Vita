<?php

/* layout.twig */
class __TwigTemplate_182f8fa33220b6e5c3078cd184d74c1e8f1fdc765ffeabaa0b4d0851157a260a extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'styles' => array($this, 'block_styles'),
            'body_class' => array($this, 'block_body_class'),
            'content' => array($this, 'block_content'),
            'scripts' => array($this, 'block_scripts'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html>
<head>
    <meta charset=\"utf-8\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
    <title>AdminLTE 2 | Blank Page</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content=\"width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no\" name=\"viewport\">
    <!-- Bootstrap 3.3.6 -->
    <link rel=\"stylesheet\" href=\"";
        // line 10
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "bootstrap/css/bootstrap.min.css\">
    <!-- Font Awesome -->
    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css\">
    <!-- Ionicons -->
    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css\">
    <!-- DataTables -->
    <link rel=\"stylesheet\" href=\"";
        // line 16
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "plugins/datatables/dataTables.bootstrap.css\">
    <!-- Theme style -->
    <link rel=\"stylesheet\" href=\"";
        // line 18
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "dist/css/AdminLTE.min.css\">
    <!-- iCheck -->
    <link rel=\"stylesheet\" href=\"";
        // line 20
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "plugins/iCheck/square/blue.css\">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
    folder instead of downloading all of them to reduce the load. -->
    <link rel=\"stylesheet\" href=\"";
        // line 23
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "dist/css/skins/_all-skins.min.css\">
    <link rel=\"stylesheet\" href=\"";
        // line 24
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "dist/css/custom.css\">
    ";
        // line 25
        $this->displayBlock('styles', $context, $blocks);
        // line 26
        echo "    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src=\"https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js\"></script>
    <script src=\"https://oss.maxcdn.com/respond/1.4.2/respond.min.js\"></script>
    <![endif]-->
</head>
<body class=\"";
        // line 34
        $this->displayBlock('body_class', $context, $blocks);
        echo " hold-transition skin-blue sidebar-mini\">

";
        // line 36
        $this->displayBlock('content', $context, $blocks);
        // line 37
        echo "
<!-- jQuery 2.2.3 -->
<script src=\"";
        // line 39
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "plugins/jQuery/jquery-2.2.3.min.js\"></script>
<!-- Bootstrap 3.3.6 -->
<script src=\"";
        // line 41
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "bootstrap/js/bootstrap.min.js\"></script>
<!-- SlimScroll -->
<script src=\"";
        // line 43
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "plugins/slimScroll/jquery.slimscroll.min.js\"></script>
<!-- FastClick -->
<script src=\"";
        // line 45
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "plugins/fastclick/fastclick.js\"></script>
<!-- AdminLTE App -->
<script src=\"";
        // line 47
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "dist/js/app.min.js\"></script>
<!-- AdminLTE for demo purposes -->
<script src=\"";
        // line 49
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "dist/js/demo.js\"></script>

";
        // line 51
        $this->displayBlock('scripts', $context, $blocks);
        // line 52
        echo "
</body>
</html>
";
    }

    // line 25
    public function block_styles($context, array $blocks = array())
    {
    }

    // line 34
    public function block_body_class($context, array $blocks = array())
    {
    }

    // line 36
    public function block_content($context, array $blocks = array())
    {
    }

    // line 51
    public function block_scripts($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "layout.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  144 => 51,  139 => 36,  134 => 34,  129 => 25,  122 => 52,  120 => 51,  115 => 49,  110 => 47,  105 => 45,  100 => 43,  95 => 41,  90 => 39,  86 => 37,  84 => 36,  79 => 34,  69 => 26,  67 => 25,  63 => 24,  59 => 23,  53 => 20,  48 => 18,  43 => 16,  34 => 10,  23 => 1,);
    }
}
/* <!DOCTYPE html>*/
/* <html>*/
/* <head>*/
/*     <meta charset="utf-8">*/
/*     <meta http-equiv="X-UA-Compatible" content="IE=edge">*/
/*     <title>AdminLTE 2 | Blank Page</title>*/
/*     <!-- Tell the browser to be responsive to screen width -->*/
/*     <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">*/
/*     <!-- Bootstrap 3.3.6 -->*/
/*     <link rel="stylesheet" href="{{ vita.config.template_url }}bootstrap/css/bootstrap.min.css">*/
/*     <!-- Font Awesome -->*/
/*     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">*/
/*     <!-- Ionicons -->*/
/*     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">*/
/*     <!-- DataTables -->*/
/*     <link rel="stylesheet" href="{{ vita.config.template_url }}plugins/datatables/dataTables.bootstrap.css">*/
/*     <!-- Theme style -->*/
/*     <link rel="stylesheet" href="{{ vita.config.template_url }}dist/css/AdminLTE.min.css">*/
/*     <!-- iCheck -->*/
/*     <link rel="stylesheet" href="{{ vita.config.template_url }}plugins/iCheck/square/blue.css">*/
/*     <!-- AdminLTE Skins. Choose a skin from the css/skins*/
/*     folder instead of downloading all of them to reduce the load. -->*/
/*     <link rel="stylesheet" href="{{ vita.config.template_url }}dist/css/skins/_all-skins.min.css">*/
/*     <link rel="stylesheet" href="{{ vita.config.template_url }}dist/css/custom.css">*/
/*     {% block styles %}{% endblock %}*/
/*     */
/*     <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->*/
/*     <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->*/
/*     <!--[if lt IE 9]>*/
/*     <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>*/
/*     <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>*/
/*     <![endif]-->*/
/* </head>*/
/* <body class="{% block body_class %}{% endblock %} hold-transition skin-blue sidebar-mini">*/
/* */
/* {% block content %}{% endblock %}*/
/* */
/* <!-- jQuery 2.2.3 -->*/
/* <script src="{{ vita.config.template_url }}plugins/jQuery/jquery-2.2.3.min.js"></script>*/
/* <!-- Bootstrap 3.3.6 -->*/
/* <script src="{{ vita.config.template_url }}bootstrap/js/bootstrap.min.js"></script>*/
/* <!-- SlimScroll -->*/
/* <script src="{{ vita.config.template_url }}plugins/slimScroll/jquery.slimscroll.min.js"></script>*/
/* <!-- FastClick -->*/
/* <script src="{{ vita.config.template_url }}plugins/fastclick/fastclick.js"></script>*/
/* <!-- AdminLTE App -->*/
/* <script src="{{ vita.config.template_url }}dist/js/app.min.js"></script>*/
/* <!-- AdminLTE for demo purposes -->*/
/* <script src="{{ vita.config.template_url }}dist/js/demo.js"></script>*/
/* */
/* {% block scripts %}{% endblock %}*/
/* */
/* </body>*/
/* </html>*/
/* */
