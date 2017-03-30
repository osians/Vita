<?php

/* index.twig */
class __TwigTemplate_734db815eabe138dd387787bb6e415185a89ea1510ef0a18375cf4038b0bf6fc extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("starter.twig", "index.twig", 1);
        $this->blocks = array(
            'styles' => array($this, 'block_styles'),
            'page_content' => array($this, 'block_page_content'),
            'scripts' => array($this, 'block_scripts'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "starter.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_styles($context, array $blocks = array())
    {
        // line 4
        echo "\t<!-- bootstrap datepicker -->
  \t<link rel=\"stylesheet\" href=\"";
        // line 5
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "plugins/datepicker/datepicker3.css\">
  \t<!-- Select2 -->
  \t<link rel=\"stylesheet\" href=\"";
        // line 7
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "plugins/select2/select2.min.css\">
";
    }

    // line 10
    public function block_page_content($context, array $blocks = array())
    {
        // line 11
        echo "
\t";
        // line 13
        echo "\t";
        if ($this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "post", array(), "any", false, true), "success", array(), "any", true, true)) {
            // line 14
            echo "\t    ";
            // line 15
            echo "\t    ";
            if ((twig_length_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "post", array()), "success", array())) > 0)) {
                // line 16
                echo "\t        
\t    \t<div class=\"alert alert-success alert-dismissible\">
\t    \t\t<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>
\t    \t\t<h4><i class=\"icon fa fa-check\"></i> Sucesso!</h4>
\t    \t\tRegistro Salvo com Sucesso
\t    \t</div>

\t    ";
            } else {
                // line 24
                echo "\t        
\t        <div class=\"alert alert-danger alert-dismissible\">
\t        \t<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>
\t        \t<h4><i class=\"icon fa fa-ban\"></i> Alert!</h4>
\t        \tErro ao salvar o Registro <br> ";
                // line 28
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "post", array()), "error_message", array()), "html", null, true);
                echo "
\t        </div>

\t    ";
            }
            // line 32
            echo "\t";
        }
        // line 33
        echo "
\t<!-- Horizontal Form -->
\t<div class=\"box box-success\">
\t\t<div class=\"box-header with-border\">
\t\t\t<h3 class=\"box-title\">Registrar Movimentação Financeira</h3>
\t\t</div>
\t\t<!-- /.box-header -->
\t\t<!-- form start -->
\t\t<form class=\"form-horizontal\" method=\"post\">
\t\t\t<input type=\"hidden\" name=\"formname\" value=\"movimentos\" readonly>
\t\t\t<input type=\"hidden\" name=\"idusuario\" value=\"";
        // line 43
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "session", array()), "idusuario", array()), "html", null, true);
        echo "\" readonly>
\t\t\t<input type=\"hidden\" name=\"status\" value=\"1\" readonly>
\t\t\t<div class=\"box-body\">
\t\t\t\t
\t\t\t\t<div class=\"form-group\">
\t\t\t\t\t<label for=\"inputEmail3\" class=\"col-sm-2 control-label\">Data</label>

\t\t\t\t\t<div class=\"col-sm-10\">

\t\t\t\t\t\t\t<div class=\"input-group date\">
\t\t\t\t\t\t\t\t<div class=\"input-group-addon\">
\t\t\t\t\t\t\t\t\t<i class=\"fa fa-calendar\"></i>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t<input type=\"text\" name=\"data\" class=\"form-control pull-right\" id=\"datepicker\" required>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t<!-- /.input group -->
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<div class=\"form-group\">

\t\t\t\t\t<label for=\"inputEmail3\" class=\"col-sm-2 control-label\">Referente</label>
\t\t\t\t\t<div class=\"col-sm-10\">
\t\t\t\t\t\t<select name=\"idcategoria\" class=\"form-control select2\" style=\"width: 100%;\" required=\"\">
\t\t\t\t\t\t\t<option value=\"0\">Selecione</option>\t\t\t\t\t\t\t
\t\t\t\t\t\t\t";
        // line 68
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["categorias"]) ? $context["categorias"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["cat"]) {
            if ($this->getAttribute($context["cat"], "status", array())) {
                // line 69
                echo "\t\t\t\t\t\t\t\t<option value=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["cat"], "idsubcategoria", array()), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, $this->getAttribute($context["cat"], "catsub", array()), "html", null, true);
                echo "</option>
\t\t\t\t\t\t\t";
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['cat'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 71
        echo "\t\t\t\t\t\t</select>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<div class=\"form-group\">
\t\t\t\t\t<label for=\"inputEmail3\" class=\"col-sm-2 control-label\">Valor</label>
\t\t\t\t\t<div class=\"col-sm-10\">
\t\t\t\t\t\t<div class=\"input-group\">
\t\t\t\t\t\t\t<span class=\"input-group-addon\">R\$</span>
\t\t\t\t\t\t\t<input name=\"valor\" type=\"text\" class=\"form-control\" placeholder=\"valor\" required>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<div class=\"form-group\">
\t\t\t\t\t<label for=\"inputEmail3\" class=\"col-sm-2 control-label\">Liquidado</label>
\t\t\t\t\t<div class=\"col-sm-10\">
\t\t\t\t\t\t<select name=\"liquidado\" class=\"form-control\" required>
\t\t\t\t\t\t\t<option value=\"SIM\">Sim</option>
\t\t\t\t\t\t\t<option value=\"NAO\">Não</option>
\t\t\t\t\t\t</select>
\t\t\t\t\t</div>
\t\t\t\t</div>

\t\t\t\t<div class=\"form-group\">
\t\t\t\t\t<label for=\"descricao\" class=\"col-sm-2 control-label\">Descrição</label>

\t\t\t\t\t<div class=\"col-sm-10\">
\t\t\t\t\t\t<input type=\"text\" name=\"descricao\" class=\"form-control\" id=\"descricao\" placeholder=\"descrição opcional...\">
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<!-- /.box-body -->
\t\t\t<div class=\"box-footer\">
\t\t\t\t<button type=\"reset\" class=\"btn btn-default\">Cancelar</button>
\t\t\t\t<button type=\"submit\" class=\"btn btn-success pull-right\"> Registrar </button>
\t\t\t</div>
\t\t\t<!-- /.box-footer -->
\t\t</form>
\t</div> <!-- /.box success -->


\t<div class=\"row\">
\t\t<div class=\"col-xs-12\">

\t\t\t<div class=\"box\">
\t\t\t\t<div class=\"box-header\">
\t\t\t\t\t<h3 class=\"box-title\">Movimentações registradas</h3>
\t\t\t\t</div>
\t\t\t\t<!-- /.box-header -->
\t\t\t\t<div class=\"box-body\">
\t\t\t\t\t<table id=\"example1\" class=\"table table-bordered table-striped\">
\t\t\t\t\t\t<thead>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<th>Data</th>
\t\t\t\t\t\t\t\t<th>Referente a</th>
\t\t\t\t\t\t\t\t<th>Valor (R\$)</th>
\t\t\t\t\t\t\t\t<th>Liquidado</th>
\t\t\t\t\t\t\t\t<th>Descrição</th>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t</thead>
\t\t\t\t\t\t<tbody>

\t\t\t\t\t\t\t";
        // line 134
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["movimentacao"]) ? $context["movimentacao"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["movimento"]) {
            // line 135
            echo "\t\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t\t<td>";
            // line 136
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->getAttribute($context["movimento"], "data", array()), "d/m/Y"), "html", null, true);
            echo "</td>
\t\t\t\t\t\t\t\t\t<td>";
            // line 137
            echo twig_escape_filter($this->env, $this->getAttribute($context["movimento"], "categoria_sub", array()), "html", null, true);
            echo "</td>
\t\t\t\t\t\t\t\t\t<td>";
            // line 138
            echo twig_escape_filter($this->env, twig_number_format_filter($this->env, $this->getAttribute($context["movimento"], "valor", array()), 2, ",", "."), "html", null, true);
            echo "</td>
\t\t\t\t\t\t\t\t\t<td>";
            // line 139
            echo twig_escape_filter($this->env, $this->getAttribute($context["movimento"], "liquidado", array()), "html", null, true);
            echo "</td>
\t\t\t\t\t\t\t\t\t<td>";
            // line 140
            echo twig_escape_filter($this->env, $this->getAttribute($context["movimento"], "descricao", array()), "html", null, true);
            echo "</td>
\t\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['movimento'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 143
        echo "
\t\t\t\t\t\t</tbody>
\t\t\t\t\t\t<tfoot>
\t\t\t\t\t\t\t<tr>
\t\t\t\t\t\t\t\t<th>Data</th>
\t\t\t\t\t\t\t\t<th>Referente a</th>
\t\t\t\t\t\t\t\t<th>Valor (R\$)</th>
\t\t\t\t\t\t\t\t<th>Liquidado</th>
\t\t\t\t\t\t\t\t<th>Descrição</th>
\t\t\t\t\t\t\t</tr>
\t\t\t\t\t\t</tfoot>
\t\t\t\t\t</table>
\t\t\t\t</div><!-- /.box-body -->
\t\t\t</div><!-- /.box -->
\t\t</div><!-- /.col -->
\t</div><!-- /.row -->



";
    }

    // line 164
    public function block_scripts($context, array $blocks = array())
    {
        // line 165
        echo "
<!-- bootstrap datepicker -->
<script src=\"";
        // line 167
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "plugins/datepicker/bootstrap-datepicker.js\"></script>
<!-- Select2 -->
<script src=\"";
        // line 169
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "plugins/select2/select2.full.min.js\"></script>
<!-- DataTables -->
<script src=\"";
        // line 171
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "plugins/datatables/jquery.dataTables.min.js\"></script>
<script src=\"";
        // line 172
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["vita"]) ? $context["vita"] : null), "config", array()), "template_url", array()), "html", null, true);
        echo "plugins/datatables/dataTables.bootstrap.min.js\"></script>
<script>
\t\$(function(){
\t\t
\t\t//Date picker
\t\t\$('#datepicker').datepicker({
\t\t\tautoclose: true
\t\t});
    \t
    \t//Initialize Select2 Elements
    \t\$(\".select2\").select2();

    \t// dado partida no datatable
\t\t\$(\"#example1\").DataTable();
\t\t\$('#example2').DataTable({
\t\t\t\"paging\": true,
\t\t\t\"lengthChange\": false,
\t\t\t\"searching\": false,
\t\t\t\"ordering\": true,
\t\t\t\"info\": true,
\t\t\t\"autoWidth\": false
\t\t});
    
\t});
</script>

";
    }

    public function getTemplateName()
    {
        return "index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  285 => 172,  281 => 171,  276 => 169,  271 => 167,  267 => 165,  264 => 164,  241 => 143,  232 => 140,  228 => 139,  224 => 138,  220 => 137,  216 => 136,  213 => 135,  209 => 134,  144 => 71,  132 => 69,  127 => 68,  99 => 43,  87 => 33,  84 => 32,  77 => 28,  71 => 24,  61 => 16,  58 => 15,  56 => 14,  53 => 13,  50 => 11,  47 => 10,  41 => 7,  36 => 5,  33 => 4,  30 => 3,  11 => 1,);
    }
}
/* {% extends 'starter.twig' %}*/
/* */
/* {% block styles %}*/
/* 	<!-- bootstrap datepicker -->*/
/*   	<link rel="stylesheet" href="{{ vita.config.template_url }}plugins/datepicker/datepicker3.css">*/
/*   	<!-- Select2 -->*/
/*   	<link rel="stylesheet" href="{{ vita.config.template_url }}plugins/select2/select2.min.css">*/
/* {% endblock %}*/
/* */
/* {% block page_content %}*/
/* */
/* 	{# se a variavel post success esta definida #}*/
/* 	{% if vita.post.success is defined %}*/
/* 	    {# se o resultado nao for falso #}*/
/* 	    {% if vita.post.success|length > 0 %}*/
/* 	        */
/* 	    	<div class="alert alert-success alert-dismissible">*/
/* 	    		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>*/
/* 	    		<h4><i class="icon fa fa-check"></i> Sucesso!</h4>*/
/* 	    		Registro Salvo com Sucesso*/
/* 	    	</div>*/
/* */
/* 	    {% else %}*/
/* 	        */
/* 	        <div class="alert alert-danger alert-dismissible">*/
/* 	        	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>*/
/* 	        	<h4><i class="icon fa fa-ban"></i> Alert!</h4>*/
/* 	        	Erro ao salvar o Registro <br> {{ vita.post.error_message }}*/
/* 	        </div>*/
/* */
/* 	    {% endif %}*/
/* 	{% endif %}*/
/* */
/* 	<!-- Horizontal Form -->*/
/* 	<div class="box box-success">*/
/* 		<div class="box-header with-border">*/
/* 			<h3 class="box-title">Registrar Movimentação Financeira</h3>*/
/* 		</div>*/
/* 		<!-- /.box-header -->*/
/* 		<!-- form start -->*/
/* 		<form class="form-horizontal" method="post">*/
/* 			<input type="hidden" name="formname" value="movimentos" readonly>*/
/* 			<input type="hidden" name="idusuario" value="{{ vita.session.idusuario }}" readonly>*/
/* 			<input type="hidden" name="status" value="1" readonly>*/
/* 			<div class="box-body">*/
/* 				*/
/* 				<div class="form-group">*/
/* 					<label for="inputEmail3" class="col-sm-2 control-label">Data</label>*/
/* */
/* 					<div class="col-sm-10">*/
/* */
/* 							<div class="input-group date">*/
/* 								<div class="input-group-addon">*/
/* 									<i class="fa fa-calendar"></i>*/
/* 								</div>*/
/* 								<input type="text" name="data" class="form-control pull-right" id="datepicker" required>*/
/* 							</div>*/
/* 							<!-- /.input group -->*/
/* 					</div>*/
/* 				</div>*/
/* */
/* 				<div class="form-group">*/
/* */
/* 					<label for="inputEmail3" class="col-sm-2 control-label">Referente</label>*/
/* 					<div class="col-sm-10">*/
/* 						<select name="idcategoria" class="form-control select2" style="width: 100%;" required="">*/
/* 							<option value="0">Selecione</option>							*/
/* 							{% for cat in categorias if cat.status %}*/
/* 								<option value="{{ cat.idsubcategoria }}">{{ cat.catsub }}</option>*/
/* 							{% endfor %}*/
/* 						</select>*/
/* 					</div>*/
/* 				</div>*/
/* */
/* 				<div class="form-group">*/
/* 					<label for="inputEmail3" class="col-sm-2 control-label">Valor</label>*/
/* 					<div class="col-sm-10">*/
/* 						<div class="input-group">*/
/* 							<span class="input-group-addon">R$</span>*/
/* 							<input name="valor" type="text" class="form-control" placeholder="valor" required>*/
/* 						</div>*/
/* 					</div>*/
/* 				</div>*/
/* */
/* 				<div class="form-group">*/
/* 					<label for="inputEmail3" class="col-sm-2 control-label">Liquidado</label>*/
/* 					<div class="col-sm-10">*/
/* 						<select name="liquidado" class="form-control" required>*/
/* 							<option value="SIM">Sim</option>*/
/* 							<option value="NAO">Não</option>*/
/* 						</select>*/
/* 					</div>*/
/* 				</div>*/
/* */
/* 				<div class="form-group">*/
/* 					<label for="descricao" class="col-sm-2 control-label">Descrição</label>*/
/* */
/* 					<div class="col-sm-10">*/
/* 						<input type="text" name="descricao" class="form-control" id="descricao" placeholder="descrição opcional...">*/
/* 					</div>*/
/* 				</div>*/
/* 			</div>*/
/* 			<!-- /.box-body -->*/
/* 			<div class="box-footer">*/
/* 				<button type="reset" class="btn btn-default">Cancelar</button>*/
/* 				<button type="submit" class="btn btn-success pull-right"> Registrar </button>*/
/* 			</div>*/
/* 			<!-- /.box-footer -->*/
/* 		</form>*/
/* 	</div> <!-- /.box success -->*/
/* */
/* */
/* 	<div class="row">*/
/* 		<div class="col-xs-12">*/
/* */
/* 			<div class="box">*/
/* 				<div class="box-header">*/
/* 					<h3 class="box-title">Movimentações registradas</h3>*/
/* 				</div>*/
/* 				<!-- /.box-header -->*/
/* 				<div class="box-body">*/
/* 					<table id="example1" class="table table-bordered table-striped">*/
/* 						<thead>*/
/* 							<tr>*/
/* 								<th>Data</th>*/
/* 								<th>Referente a</th>*/
/* 								<th>Valor (R$)</th>*/
/* 								<th>Liquidado</th>*/
/* 								<th>Descrição</th>*/
/* 							</tr>*/
/* 						</thead>*/
/* 						<tbody>*/
/* */
/* 							{% for movimento in movimentacao %}*/
/* 								<tr>*/
/* 									<td>{{ movimento.data | date('d/m/Y') }}</td>*/
/* 									<td>{{ movimento.categoria_sub }}</td>*/
/* 									<td>{{ movimento.valor|number_format(2,',','.') }}</td>*/
/* 									<td>{{ movimento.liquidado }}</td>*/
/* 									<td>{{ movimento.descricao }}</td>*/
/* 								</tr>*/
/* 							{% endfor %}*/
/* */
/* 						</tbody>*/
/* 						<tfoot>*/
/* 							<tr>*/
/* 								<th>Data</th>*/
/* 								<th>Referente a</th>*/
/* 								<th>Valor (R$)</th>*/
/* 								<th>Liquidado</th>*/
/* 								<th>Descrição</th>*/
/* 							</tr>*/
/* 						</tfoot>*/
/* 					</table>*/
/* 				</div><!-- /.box-body -->*/
/* 			</div><!-- /.box -->*/
/* 		</div><!-- /.col -->*/
/* 	</div><!-- /.row -->*/
/* */
/* */
/* */
/* {% endblock %}*/
/* */
/* {% block scripts %}*/
/* */
/* <!-- bootstrap datepicker -->*/
/* <script src="{{ vita.config.template_url }}plugins/datepicker/bootstrap-datepicker.js"></script>*/
/* <!-- Select2 -->*/
/* <script src="{{ vita.config.template_url }}plugins/select2/select2.full.min.js"></script>*/
/* <!-- DataTables -->*/
/* <script src="{{ vita.config.template_url }}plugins/datatables/jquery.dataTables.min.js"></script>*/
/* <script src="{{ vita.config.template_url }}plugins/datatables/dataTables.bootstrap.min.js"></script>*/
/* <script>*/
/* 	$(function(){*/
/* 		*/
/* 		//Date picker*/
/* 		$('#datepicker').datepicker({*/
/* 			autoclose: true*/
/* 		});*/
/*     	*/
/*     	//Initialize Select2 Elements*/
/*     	$(".select2").select2();*/
/* */
/*     	// dado partida no datatable*/
/* 		$("#example1").DataTable();*/
/* 		$('#example2').DataTable({*/
/* 			"paging": true,*/
/* 			"lengthChange": false,*/
/* 			"searching": false,*/
/* 			"ordering": true,*/
/* 			"info": true,*/
/* 			"autoWidth": false*/
/* 		});*/
/*     */
/* 	});*/
/* </script>*/
/* */
/* {% endblock %}*/
