<?php

/* starter.twig */
class __TwigTemplate_181681070b1bc78488b54d3037d61c426e83d1661fd6587e48f6f6a537a18830 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("layout.twig", "starter.twig", 1);
        $this->blocks = array(
            'styles' => array($this, 'block_styles'),
            'content' => array($this, 'block_content'),
            'page_content' => array($this, 'block_page_content'),
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
    public function block_styles($context, array $blocks = array())
    {
    }

    // line 5
    public function block_content($context, array $blocks = array())
    {
        // line 6
        echo "
    <!-- Site wrapper -->
    <div class=\"wrapper\">

        <header class=\"main-header\">
            <!-- Logo -->
            <a href=\"";
        // line 12
        echo twig_escape_filter($this->env, (isset($context["base_url"]) ? $context["base_url"] : null), "html", null, true);
        echo "\" class=\"logo\">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class=\"logo-mini\"><b>A</b>LT</span>
                <!-- logo for regular state and mobile devices -->
                <span class=\"logo-lg\"><b>Admin</b>LTE</span>
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class=\"navbar navbar-static-top\">
                <!-- Sidebar toggle button-->
                <a href=\"#\" class=\"sidebar-toggle\" data-toggle=\"offcanvas\" role=\"button\">
                    <span class=\"sr-only\">Toggle navigation</span>
                    <span class=\"icon-bar\"></span>
                    <span class=\"icon-bar\"></span>
                    <span class=\"icon-bar\"></span>
                </a>

                <div class=\"navbar-custom-menu\">
                    <ul class=\"nav navbar-nav\"> 
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class=\"dropdown user user-menu\">
                            <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">
                                <img src=\"";
        // line 33
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "dist/img/user2-160x160.jpg\" class=\"user-image\" alt=\"User Image\">
                                <span class=\"hidden-xs\">Alexander Pierce</span>
                            </a>
                            <ul class=\"dropdown-menu\">
                                <!-- User image -->
                                <li class=\"user-header\">
                                    <img src=\"";
        // line 39
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "dist/img/user2-160x160.jpg\" class=\"img-circle\" alt=\"User Image\">

                                    <p>
                                        ";
        // line 42
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "session", array()), "nome", array()), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "session", array()), "sobrenome", array()), "html", null, true);
        echo "
                                        <small>Member since Nov. 2012</small>
                                    </p>
                                </li>
                                <!-- Menu Body -->
                                <li class=\"user-body\">
                                    <div class=\"row\">
                                        <div class=\"col-xs-4 text-center\">
                                            <a href=\"#\">Followers</a>
                                        </div>
                                        <div class=\"col-xs-4 text-center\">
                                            <a href=\"#\">Sales</a>
                                        </div>
                                        <div class=\"col-xs-4 text-center\">
                                            <a href=\"#\">Friends</a>
                                        </div>
                                    </div>
                                    <!-- /.row -->
                                </li>
                                <!-- Menu Footer-->
                                <li class=\"user-footer\">
                                    <div class=\"pull-left\">
                                        <a href=\"#\" class=\"btn btn-default btn-flat\">Profile</a>
                                    </div>
                                    <div class=\"pull-right\">
                                        <a href=\"";
        // line 67
        echo twig_escape_filter($this->env, (isset($context["base_url"]) ? $context["base_url"] : null), "html", null, true);
        echo "Index/logout\" class=\"btn btn-default btn-flat\">Sign out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- Control Sidebar Toggle Button -->
                        <li>
                            <a href=\"#\" data-toggle=\"control-sidebar\"><i class=\"fa fa-gears\"></i></a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <!-- =============================================== -->

        <!-- Left side column. contains the sidebar -->
        <aside class=\"main-sidebar\">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class=\"sidebar\">
                <!-- Sidebar user panel -->
                <div class=\"user-panel\">
                    <div class=\"pull-left image\">
                        <img src=\"";
        // line 90
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "dist/img/user2-160x160.jpg\" class=\"img-circle\" alt=\"User Image\">
                    </div>
                    <div class=\"pull-left info\">
                        <p>Alexander Pierce</p>
                        <a href=\"#\"><i class=\"fa fa-circle text-success\"></i> Online</a>
                    </div>
                </div>

                <!-- sidebar menu: : style can be found in sidebar.less -->
                <ul class=\"sidebar-menu\">
                    <li class=\"header\">MAIN NAVIGATION</li>
                    <li><a href=\"";
        // line 101
        echo twig_escape_filter($this->env, (isset($context["base_url"]) ? $context["base_url"] : null), "html", null, true);
        echo "\"><i class=\"fa fa-book\"></i> <span>Home</span></a></li>
                    <li class=\"treeview\">
                        <a href=\"#\">
                            <i class=\"fa fa-pie-chart\"></i> 
                            <span>Relatórios Gráficos</span>
                            <span class=\"pull-right-container\">
                                <i class=\"fa fa-angle-left pull-right\"></i>
                            </span>
                        </a>
                        <ul class=\"treeview-menu\">
                            <li><a href=\"";
        // line 111
        echo twig_escape_filter($this->env, (isset($context["base_url"]) ? $context["base_url"] : null), "html", null, true);
        echo "\"><i class=\"fa fa-circle-o\"></i> Percentual de Crescimento</a></li>
                            <li><a href=\"";
        // line 112
        echo twig_escape_filter($this->env, (isset($context["base_url"]) ? $context["base_url"] : null), "html", null, true);
        echo "relatorios/rendimentoxdespesas\"><i class=\"fa fa-circle-o\"></i> Rendimentos x Despesas</a></li>
                            <li><a href=\"";
        // line 113
        echo twig_escape_filter($this->env, (isset($context["base_url"]) ? $context["base_url"] : null), "html", null, true);
        echo "\"><i class=\"fa fa-circle-o\"></i> Evolução dos gastos</a></li>
                            <li><a href=\"";
        // line 114
        echo twig_escape_filter($this->env, (isset($context["base_url"]) ? $context["base_url"] : null), "html", null, true);
        echo "\"><i class=\"fa fa-circle-o\"></i> Para onde Vai</a></li>
                            <li><a href=\"";
        // line 115
        echo twig_escape_filter($this->env, (isset($context["base_url"]) ? $context["base_url"] : null), "html", null, true);
        echo "\"><i class=\"fa fa-circle-o\"></i> Gráficos</a></li>
                        </ul>
                    </li>
                    <li><a href=\"";
        // line 118
        echo twig_escape_filter($this->env, (isset($context["base_url"]) ? $context["base_url"] : null), "html", null, true);
        echo "Index/logout\"><i class=\"fa fa-book\"></i> <span>Logout</span></a></li>
                </ul>
            </section>
            <!-- /.sidebar -->
        </aside>

        <!-- =============================================== -->

        <!-- Content Wrapper. Contains page content -->
        <div class=\"content-wrapper\">
            <!-- Content Header (Page header) -->
            <section class=\"content-header\">
                <h1>
                    Blank page
                    <small>it all starts here</small>
                </h1>
                <ol class=\"breadcrumb\">
                    <li><a href=\"#\"><i class=\"fa fa-dashboard\"></i> Home</a></li>
                    <li><a href=\"#\">Examples</a></li>
                    <li class=\"active\">Blank page</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class=\"content\">

                ";
        // line 144
        $this->displayBlock('page_content', $context, $blocks);
        // line 145
        echo "
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <footer class=\"main-footer\">
            <div class=\"pull-right hidden-xs\">
                <b>Version</b> 2.3.6
            </div>
            <strong>Copyright &copy; 2014-2016 <a href=\"http://almsaeedstudio.com\">Almsaeed Studio</a>.</strong> All rights
            reserved.
        </footer>

                <!-- Control Sidebar -->
                <aside class=\"control-sidebar control-sidebar-dark\">
                    <!-- Create the tabs -->
                    <ul class=\"nav nav-tabs nav-justified control-sidebar-tabs\">
                        <li><a href=\"#control-sidebar-home-tab\" data-toggle=\"tab\"><i class=\"fa fa-home\"></i></a></li>

                        <li><a href=\"#control-sidebar-settings-tab\" data-toggle=\"tab\"><i class=\"fa fa-gears\"></i></a></li>
                    </ul>
                    <!-- Tab panes -->
                    <div class=\"tab-content\">
                        <!-- Home tab content -->
                        <div class=\"tab-pane\" id=\"control-sidebar-home-tab\">
                            <h3 class=\"control-sidebar-heading\">Recent Activity</h3>
                            <ul class=\"control-sidebar-menu\">
                                <li>
                                    <a href=\"javascript:void(0)\">
                                        <i class=\"menu-icon fa fa-birthday-cake bg-red\"></i>

                                        <div class=\"menu-info\">
                                            <h4 class=\"control-sidebar-subheading\">Langdon's Birthday</h4>

                                            <p>Will be 23 on April 24th</p>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href=\"javascript:void(0)\">
                                        <i class=\"menu-icon fa fa-user bg-yellow\"></i>

                                        <div class=\"menu-info\">
                                            <h4 class=\"control-sidebar-subheading\">Frodo Updated His Profile</h4>

                                            <p>New phone +1(800)555-1234</p>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href=\"javascript:void(0)\">
                                        <i class=\"menu-icon fa fa-envelope-o bg-light-blue\"></i>

                                        <div class=\"menu-info\">
                                            <h4 class=\"control-sidebar-subheading\">Nora Joined Mailing List</h4>

                                            <p>nora@example.com</p>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href=\"javascript:void(0)\">
                                        <i class=\"menu-icon fa fa-file-code-o bg-green\"></i>

                                        <div class=\"menu-info\">
                                            <h4 class=\"control-sidebar-subheading\">Cron Job 254 Executed</h4>

                                            <p>Execution time 5 seconds</p>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                            <!-- /.control-sidebar-menu -->

                            <h3 class=\"control-sidebar-heading\">Tasks Progress</h3>
                            <ul class=\"control-sidebar-menu\">
                                <li>
                                    <a href=\"javascript:void(0)\">
                                        <h4 class=\"control-sidebar-subheading\">
                                            Custom Template Design
                                            <span class=\"label label-danger pull-right\">70%</span>
                                        </h4>

                                        <div class=\"progress progress-xxs\">
                                            <div class=\"progress-bar progress-bar-danger\" style=\"width: 70%\"></div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href=\"javascript:void(0)\">
                                        <h4 class=\"control-sidebar-subheading\">
                                            Update Resume
                                            <span class=\"label label-success pull-right\">95%</span>
                                        </h4>

                                        <div class=\"progress progress-xxs\">
                                            <div class=\"progress-bar progress-bar-success\" style=\"width: 95%\"></div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href=\"javascript:void(0)\">
                                        <h4 class=\"control-sidebar-subheading\">
                                            Laravel Integration
                                            <span class=\"label label-warning pull-right\">50%</span>
                                        </h4>

                                        <div class=\"progress progress-xxs\">
                                            <div class=\"progress-bar progress-bar-warning\" style=\"width: 50%\"></div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href=\"javascript:void(0)\">
                                        <h4 class=\"control-sidebar-subheading\">
                                            Back End Framework
                                            <span class=\"label label-primary pull-right\">68%</span>
                                        </h4>

                                        <div class=\"progress progress-xxs\">
                                            <div class=\"progress-bar progress-bar-primary\" style=\"width: 68%\"></div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                            <!-- /.control-sidebar-menu -->

                        </div>
                        <!-- /.tab-pane -->
                        <!-- Stats tab content -->
                        <div class=\"tab-pane\" id=\"control-sidebar-stats-tab\">Stats Tab Content</div>
                        <!-- /.tab-pane -->
                        <!-- Settings tab content -->
                        <div class=\"tab-pane\" id=\"control-sidebar-settings-tab\">
                            <form method=\"post\">
                                <h3 class=\"control-sidebar-heading\">General Settings</h3>

                                <div class=\"form-group\">
                                    <label class=\"control-sidebar-subheading\">
                                        Report panel usage
                                        <input type=\"checkbox\" class=\"pull-right\" checked>
                                    </label>

                                    <p>
                                        Some information about this general settings option
                                    </p>
                                </div>
                                <!-- /.form-group -->

                                <div class=\"form-group\">
                                    <label class=\"control-sidebar-subheading\">
                                        Allow mail redirect
                                        <input type=\"checkbox\" class=\"pull-right\" checked>
                                    </label>

                                    <p>
                                        Other sets of options are available
                                    </p>
                                </div>
                                <!-- /.form-group -->

                                <div class=\"form-group\">
                                    <label class=\"control-sidebar-subheading\">
                                        Expose author name in posts
                                        <input type=\"checkbox\" class=\"pull-right\" checked>
                                    </label>

                                    <p>
                                        Allow the user to show his name in blog posts
                                    </p>
                                </div>
                                <!-- /.form-group -->

                                <h3 class=\"control-sidebar-heading\">Chat Settings</h3>

                                <div class=\"form-group\">
                                    <label class=\"control-sidebar-subheading\">
                                        Show me as online
                                        <input type=\"checkbox\" class=\"pull-right\" checked>
                                    </label>
                                </div>
                                <!-- /.form-group -->

                                <div class=\"form-group\">
                                    <label class=\"control-sidebar-subheading\">
                                        Turn off notifications
                                        <input type=\"checkbox\" class=\"pull-right\">
                                    </label>
                                </div>
                                <!-- /.form-group -->

                                <div class=\"form-group\">
                                    <label class=\"control-sidebar-subheading\">
                                        Delete chat history
                                        <a href=\"javascript:void(0)\" class=\"text-red pull-right\"><i class=\"fa fa-trash-o\"></i></a>
                                    </label>
                                </div>
                                <!-- /.form-group -->
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                    </div>
                </aside>
                <!-- /.control-sidebar -->
<!-- Add the sidebar's background. This div must be placed
    immediately after the control sidebar -->
    <div class=\"control-sidebar-bg\"></div>
</div>
<!-- ./wrapper -->

";
    }

    // line 144
    public function block_page_content($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "starter.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  436 => 144,  221 => 145,  219 => 144,  190 => 118,  184 => 115,  180 => 114,  176 => 113,  172 => 112,  168 => 111,  155 => 101,  141 => 90,  115 => 67,  85 => 42,  79 => 39,  70 => 33,  46 => 12,  38 => 6,  35 => 5,  30 => 3,  11 => 1,);
    }
}
/* {% extends 'layout.twig' %}*/
/* */
/* {% block styles %}{% endblock %}*/
/* */
/* {% block content %}*/
/* */
/*     <!-- Site wrapper -->*/
/*     <div class="wrapper">*/
/* */
/*         <header class="main-header">*/
/*             <!-- Logo -->*/
/*             <a href="{{ base_url }}" class="logo">*/
/*                 <!-- mini logo for sidebar mini 50x50 pixels -->*/
/*                 <span class="logo-mini"><b>A</b>LT</span>*/
/*                 <!-- logo for regular state and mobile devices -->*/
/*                 <span class="logo-lg"><b>Admin</b>LTE</span>*/
/*             </a>*/
/*             <!-- Header Navbar: style can be found in header.less -->*/
/*             <nav class="navbar navbar-static-top">*/
/*                 <!-- Sidebar toggle button-->*/
/*                 <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">*/
/*                     <span class="sr-only">Toggle navigation</span>*/
/*                     <span class="icon-bar"></span>*/
/*                     <span class="icon-bar"></span>*/
/*                     <span class="icon-bar"></span>*/
/*                 </a>*/
/* */
/*                 <div class="navbar-custom-menu">*/
/*                     <ul class="nav navbar-nav"> */
/*                         <!-- User Account: style can be found in dropdown.less -->*/
/*                         <li class="dropdown user user-menu">*/
/*                             <a href="#" class="dropdown-toggle" data-toggle="dropdown">*/
/*                                 <img src="{{ vita.config.template_url }}dist/img/user2-160x160.jpg" class="user-image" alt="User Image">*/
/*                                 <span class="hidden-xs">Alexander Pierce</span>*/
/*                             </a>*/
/*                             <ul class="dropdown-menu">*/
/*                                 <!-- User image -->*/
/*                                 <li class="user-header">*/
/*                                     <img src="{{ vita.config.template_url }}dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">*/
/* */
/*                                     <p>*/
/*                                         {{ vita.session.nome }} {{ vita.session.sobrenome }}*/
/*                                         <small>Member since Nov. 2012</small>*/
/*                                     </p>*/
/*                                 </li>*/
/*                                 <!-- Menu Body -->*/
/*                                 <li class="user-body">*/
/*                                     <div class="row">*/
/*                                         <div class="col-xs-4 text-center">*/
/*                                             <a href="#">Followers</a>*/
/*                                         </div>*/
/*                                         <div class="col-xs-4 text-center">*/
/*                                             <a href="#">Sales</a>*/
/*                                         </div>*/
/*                                         <div class="col-xs-4 text-center">*/
/*                                             <a href="#">Friends</a>*/
/*                                         </div>*/
/*                                     </div>*/
/*                                     <!-- /.row -->*/
/*                                 </li>*/
/*                                 <!-- Menu Footer-->*/
/*                                 <li class="user-footer">*/
/*                                     <div class="pull-left">*/
/*                                         <a href="#" class="btn btn-default btn-flat">Profile</a>*/
/*                                     </div>*/
/*                                     <div class="pull-right">*/
/*                                         <a href="{{ base_url }}Index/logout" class="btn btn-default btn-flat">Sign out</a>*/
/*                                     </div>*/
/*                                 </li>*/
/*                             </ul>*/
/*                         </li>*/
/*                         <!-- Control Sidebar Toggle Button -->*/
/*                         <li>*/
/*                             <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>*/
/*                         </li>*/
/*                     </ul>*/
/*                 </div>*/
/*             </nav>*/
/*         </header>*/
/* */
/*         <!-- =============================================== -->*/
/* */
/*         <!-- Left side column. contains the sidebar -->*/
/*         <aside class="main-sidebar">*/
/*             <!-- sidebar: style can be found in sidebar.less -->*/
/*             <section class="sidebar">*/
/*                 <!-- Sidebar user panel -->*/
/*                 <div class="user-panel">*/
/*                     <div class="pull-left image">*/
/*                         <img src="{{ vita.config.template_url }}dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">*/
/*                     </div>*/
/*                     <div class="pull-left info">*/
/*                         <p>Alexander Pierce</p>*/
/*                         <a href="#"><i class="fa fa-circle text-success"></i> Online</a>*/
/*                     </div>*/
/*                 </div>*/
/* */
/*                 <!-- sidebar menu: : style can be found in sidebar.less -->*/
/*                 <ul class="sidebar-menu">*/
/*                     <li class="header">MAIN NAVIGATION</li>*/
/*                     <li><a href="{{ base_url }}"><i class="fa fa-book"></i> <span>Home</span></a></li>*/
/*                     <li class="treeview">*/
/*                         <a href="#">*/
/*                             <i class="fa fa-pie-chart"></i> */
/*                             <span>Relatórios Gráficos</span>*/
/*                             <span class="pull-right-container">*/
/*                                 <i class="fa fa-angle-left pull-right"></i>*/
/*                             </span>*/
/*                         </a>*/
/*                         <ul class="treeview-menu">*/
/*                             <li><a href="{{ base_url }}"><i class="fa fa-circle-o"></i> Percentual de Crescimento</a></li>*/
/*                             <li><a href="{{ base_url }}relatorios/rendimentoxdespesas"><i class="fa fa-circle-o"></i> Rendimentos x Despesas</a></li>*/
/*                             <li><a href="{{ base_url }}"><i class="fa fa-circle-o"></i> Evolução dos gastos</a></li>*/
/*                             <li><a href="{{ base_url }}"><i class="fa fa-circle-o"></i> Para onde Vai</a></li>*/
/*                             <li><a href="{{ base_url }}"><i class="fa fa-circle-o"></i> Gráficos</a></li>*/
/*                         </ul>*/
/*                     </li>*/
/*                     <li><a href="{{ base_url }}Index/logout"><i class="fa fa-book"></i> <span>Logout</span></a></li>*/
/*                 </ul>*/
/*             </section>*/
/*             <!-- /.sidebar -->*/
/*         </aside>*/
/* */
/*         <!-- =============================================== -->*/
/* */
/*         <!-- Content Wrapper. Contains page content -->*/
/*         <div class="content-wrapper">*/
/*             <!-- Content Header (Page header) -->*/
/*             <section class="content-header">*/
/*                 <h1>*/
/*                     Blank page*/
/*                     <small>it all starts here</small>*/
/*                 </h1>*/
/*                 <ol class="breadcrumb">*/
/*                     <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>*/
/*                     <li><a href="#">Examples</a></li>*/
/*                     <li class="active">Blank page</li>*/
/*                 </ol>*/
/*             </section>*/
/* */
/*             <!-- Main content -->*/
/*             <section class="content">*/
/* */
/*                 {% block page_content %}{% endblock %}*/
/* */
/*             </section>*/
/*             <!-- /.content -->*/
/*         </div>*/
/*         <!-- /.content-wrapper -->*/
/* */
/*         <footer class="main-footer">*/
/*             <div class="pull-right hidden-xs">*/
/*                 <b>Version</b> 2.3.6*/
/*             </div>*/
/*             <strong>Copyright &copy; 2014-2016 <a href="http://almsaeedstudio.com">Almsaeed Studio</a>.</strong> All rights*/
/*             reserved.*/
/*         </footer>*/
/* */
/*                 <!-- Control Sidebar -->*/
/*                 <aside class="control-sidebar control-sidebar-dark">*/
/*                     <!-- Create the tabs -->*/
/*                     <ul class="nav nav-tabs nav-justified control-sidebar-tabs">*/
/*                         <li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>*/
/* */
/*                         <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>*/
/*                     </ul>*/
/*                     <!-- Tab panes -->*/
/*                     <div class="tab-content">*/
/*                         <!-- Home tab content -->*/
/*                         <div class="tab-pane" id="control-sidebar-home-tab">*/
/*                             <h3 class="control-sidebar-heading">Recent Activity</h3>*/
/*                             <ul class="control-sidebar-menu">*/
/*                                 <li>*/
/*                                     <a href="javascript:void(0)">*/
/*                                         <i class="menu-icon fa fa-birthday-cake bg-red"></i>*/
/* */
/*                                         <div class="menu-info">*/
/*                                             <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>*/
/* */
/*                                             <p>Will be 23 on April 24th</p>*/
/*                                         </div>*/
/*                                     </a>*/
/*                                 </li>*/
/*                                 <li>*/
/*                                     <a href="javascript:void(0)">*/
/*                                         <i class="menu-icon fa fa-user bg-yellow"></i>*/
/* */
/*                                         <div class="menu-info">*/
/*                                             <h4 class="control-sidebar-subheading">Frodo Updated His Profile</h4>*/
/* */
/*                                             <p>New phone +1(800)555-1234</p>*/
/*                                         </div>*/
/*                                     </a>*/
/*                                 </li>*/
/*                                 <li>*/
/*                                     <a href="javascript:void(0)">*/
/*                                         <i class="menu-icon fa fa-envelope-o bg-light-blue"></i>*/
/* */
/*                                         <div class="menu-info">*/
/*                                             <h4 class="control-sidebar-subheading">Nora Joined Mailing List</h4>*/
/* */
/*                                             <p>nora@example.com</p>*/
/*                                         </div>*/
/*                                     </a>*/
/*                                 </li>*/
/*                                 <li>*/
/*                                     <a href="javascript:void(0)">*/
/*                                         <i class="menu-icon fa fa-file-code-o bg-green"></i>*/
/* */
/*                                         <div class="menu-info">*/
/*                                             <h4 class="control-sidebar-subheading">Cron Job 254 Executed</h4>*/
/* */
/*                                             <p>Execution time 5 seconds</p>*/
/*                                         </div>*/
/*                                     </a>*/
/*                                 </li>*/
/*                             </ul>*/
/*                             <!-- /.control-sidebar-menu -->*/
/* */
/*                             <h3 class="control-sidebar-heading">Tasks Progress</h3>*/
/*                             <ul class="control-sidebar-menu">*/
/*                                 <li>*/
/*                                     <a href="javascript:void(0)">*/
/*                                         <h4 class="control-sidebar-subheading">*/
/*                                             Custom Template Design*/
/*                                             <span class="label label-danger pull-right">70%</span>*/
/*                                         </h4>*/
/* */
/*                                         <div class="progress progress-xxs">*/
/*                                             <div class="progress-bar progress-bar-danger" style="width: 70%"></div>*/
/*                                         </div>*/
/*                                     </a>*/
/*                                 </li>*/
/*                                 <li>*/
/*                                     <a href="javascript:void(0)">*/
/*                                         <h4 class="control-sidebar-subheading">*/
/*                                             Update Resume*/
/*                                             <span class="label label-success pull-right">95%</span>*/
/*                                         </h4>*/
/* */
/*                                         <div class="progress progress-xxs">*/
/*                                             <div class="progress-bar progress-bar-success" style="width: 95%"></div>*/
/*                                         </div>*/
/*                                     </a>*/
/*                                 </li>*/
/*                                 <li>*/
/*                                     <a href="javascript:void(0)">*/
/*                                         <h4 class="control-sidebar-subheading">*/
/*                                             Laravel Integration*/
/*                                             <span class="label label-warning pull-right">50%</span>*/
/*                                         </h4>*/
/* */
/*                                         <div class="progress progress-xxs">*/
/*                                             <div class="progress-bar progress-bar-warning" style="width: 50%"></div>*/
/*                                         </div>*/
/*                                     </a>*/
/*                                 </li>*/
/*                                 <li>*/
/*                                     <a href="javascript:void(0)">*/
/*                                         <h4 class="control-sidebar-subheading">*/
/*                                             Back End Framework*/
/*                                             <span class="label label-primary pull-right">68%</span>*/
/*                                         </h4>*/
/* */
/*                                         <div class="progress progress-xxs">*/
/*                                             <div class="progress-bar progress-bar-primary" style="width: 68%"></div>*/
/*                                         </div>*/
/*                                     </a>*/
/*                                 </li>*/
/*                             </ul>*/
/*                             <!-- /.control-sidebar-menu -->*/
/* */
/*                         </div>*/
/*                         <!-- /.tab-pane -->*/
/*                         <!-- Stats tab content -->*/
/*                         <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>*/
/*                         <!-- /.tab-pane -->*/
/*                         <!-- Settings tab content -->*/
/*                         <div class="tab-pane" id="control-sidebar-settings-tab">*/
/*                             <form method="post">*/
/*                                 <h3 class="control-sidebar-heading">General Settings</h3>*/
/* */
/*                                 <div class="form-group">*/
/*                                     <label class="control-sidebar-subheading">*/
/*                                         Report panel usage*/
/*                                         <input type="checkbox" class="pull-right" checked>*/
/*                                     </label>*/
/* */
/*                                     <p>*/
/*                                         Some information about this general settings option*/
/*                                     </p>*/
/*                                 </div>*/
/*                                 <!-- /.form-group -->*/
/* */
/*                                 <div class="form-group">*/
/*                                     <label class="control-sidebar-subheading">*/
/*                                         Allow mail redirect*/
/*                                         <input type="checkbox" class="pull-right" checked>*/
/*                                     </label>*/
/* */
/*                                     <p>*/
/*                                         Other sets of options are available*/
/*                                     </p>*/
/*                                 </div>*/
/*                                 <!-- /.form-group -->*/
/* */
/*                                 <div class="form-group">*/
/*                                     <label class="control-sidebar-subheading">*/
/*                                         Expose author name in posts*/
/*                                         <input type="checkbox" class="pull-right" checked>*/
/*                                     </label>*/
/* */
/*                                     <p>*/
/*                                         Allow the user to show his name in blog posts*/
/*                                     </p>*/
/*                                 </div>*/
/*                                 <!-- /.form-group -->*/
/* */
/*                                 <h3 class="control-sidebar-heading">Chat Settings</h3>*/
/* */
/*                                 <div class="form-group">*/
/*                                     <label class="control-sidebar-subheading">*/
/*                                         Show me as online*/
/*                                         <input type="checkbox" class="pull-right" checked>*/
/*                                     </label>*/
/*                                 </div>*/
/*                                 <!-- /.form-group -->*/
/* */
/*                                 <div class="form-group">*/
/*                                     <label class="control-sidebar-subheading">*/
/*                                         Turn off notifications*/
/*                                         <input type="checkbox" class="pull-right">*/
/*                                     </label>*/
/*                                 </div>*/
/*                                 <!-- /.form-group -->*/
/* */
/*                                 <div class="form-group">*/
/*                                     <label class="control-sidebar-subheading">*/
/*                                         Delete chat history*/
/*                                         <a href="javascript:void(0)" class="text-red pull-right"><i class="fa fa-trash-o"></i></a>*/
/*                                     </label>*/
/*                                 </div>*/
/*                                 <!-- /.form-group -->*/
/*                             </form>*/
/*                         </div>*/
/*                         <!-- /.tab-pane -->*/
/*                     </div>*/
/*                 </aside>*/
/*                 <!-- /.control-sidebar -->*/
/* <!-- Add the sidebar's background. This div must be placed*/
/*     immediately after the control sidebar -->*/
/*     <div class="control-sidebar-bg"></div>*/
/* </div>*/
/* <!-- ./wrapper -->*/
/* */
/* {% endblock %}*/
/* */
