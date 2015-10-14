<?php
class Zend_View_Helper_Paginacao extends Zend_View_Helper_Abstract
{
    public function paginacao($pages, $page)
	{
		
		$paginas  = "<div class=\"row\">";
		$paginas .= "	<div class=\"col-sm-2 hidden-xs\">";
		$paginas .= "		<select title=\"Quantidade de registros por página\" class=\"form-control input-sm\" id=\"registros_por_pagina\" onchange=\"paginacao(1, this.value)\">";
		$paginas .= "			<option label=\"03 por página\" value=\"3\">03 por página</option>";
		$paginas .= "			<option label=\"05 por página\" value=\"5\">05 por página</option>";
		$paginas .= "			<option label=\"10 por página\" value=\"10\">10 por página</option>";
		$paginas .= "			<option label=\"20 por página\" value=\"20\">20 por página</option>";
		$paginas .= "			<option label=\"50 por página\" value=\"50\">50 por página</option>";
		$paginas .= "			<option label=\"100 por página\" value=\"100\">100 por página</option>";
		$paginas .= "		</select>";
		$paginas .= "	</div>";
		$paginas .= "	<div class=\"col-sm-10\" style=\"text-align: right;\">";
		$paginas .= "		<span class=\"adm-paginacao-text\" style=\"\"><b>{$page}</b> de <b>{$pages}</b> páginas&nbsp;</span>";
		$paginas .= "		<nav style=\"display: inline;\">";
		$paginas .= "			<ul class=\"pagination pagination-sm\">";
	
		if (isset($pages) && $pages > 1) {
			$next = $page + 1;
			$prev = $page - 1;
			if ($page < $pages && $page > 1) {
				$paginas .= "<li><a style=\"cursor:pointer;\" title=\"Primeira página\" onclick=\"paginacao(1)\"><i class=\"fa fa-angle-double-left\" style=\"font-weight: bold; color: #000000;\"></i></a></li>";
				$paginas .= "<li><a style=\"cursor:pointer;\" title=\"Anterior\" onclick=\"paginacao({$prev})\"><i class=\"fa fa-angle-left\" style=\"font-weight: bold; color: #000000;\"></i></a></li>";
				$paginas .= "<li><a style=\"cursor:pointer;\" title=\"Próxima\" onclick=\"paginacao({$next})\"><i class=\"fa fa-angle-right\" style=\"font-weight: bold; color: #000000;\"></i></a></li>";
				$paginas .= "<li><a style=\"cursor:pointer;\" title=\"Ultima página\" onclick=\"paginacao({$pages})\"><i class=\"fa fa-angle-double-right\" style=\"font-weight: bold; color: #000000;\"></i></a></li>";
			} else if ($page >= $pages) {
				$paginas .= "<li><a style=\"cursor:pointer;\" title=\"Primeira página\" onclick=\"paginacao(1)\"><i class=\"fa fa-angle-double-left\" style=\"font-weight: bold; color: #000000;\"></i></a></li>";
				$paginas .= "<li><a style=\"cursor:pointer;\" title=\"Anterior\" onclick=\"paginacao({$prev})\"><i class=\"fa fa-angle-left\" style=\"font-weight: bold; color: #000000;\"></i></a></li>";
				$paginas .= "<li title=\"Próxima\" class=\"active\"><a><i class=\"fa fa-angle-right\" style=\"font-weight: bold;\"></i></a></li>";
				$paginas .= "<li class=\"active\"><a title=\"Ultima página\"><i class=\"fa fa-angle-double-right\" style=\"font-weight: bold;\"></i></a></li>";
			} else if ($page <= 1) {
				$paginas .= "<li class=\"active\"><a title=\"Primeira página\"><i class=\"fa fa-angle-double-left\" style=\"font-weight: bold;\"></i></li>";
				$paginas .= "<li class=\"active\"><a title=\"Anterior\"><i class=\"fa fa-angle-left\" style=\"font-weight: bold;\"></i></a></li>";
				$paginas .= "<li><a style=\"cursor:pointer;\" title=\"Próxima\" onclick=\"paginacao({$next})\"><i class=\"fa fa-angle-right\" style=\"font-weight: bold; color: #000000;\"></i></a></li>";
				$paginas .= "<li><a style=\"cursor:pointer;\" title=\"Ultima página\" onclick=\"paginacao({$pages})\"><i class=\"fa fa-angle-double-right\" style=\"font-weight: bold; color: #000000;\"></i></a></li>";
			}
		} else {
			$paginas .= "<li class=\"active\"><a title=\"Primeira página\"><i class=\"fa fa-angle-double-left\" style=\"font-weight: bold;\"></i></a></li>";
			$paginas .= "<li class=\"active\"><a title=\"Anterior\"><i class=\"fa fa-angle-left\" style=\"font-weight: bold;\"></i></a></li>";
			$paginas .= "<li class=\"active\"><a title=\"Próxima\"><i class=\"fa fa-angle-right\" style=\"font-weight: bold;\"></i></a></li>";
			$paginas .= "<li class=\"active\"><a title=\"Ultima página\"><i class=\"fa fa-angle-double-right\" style=\"font-weight: bold;\"></i></a></li>";
		}
		$paginas .= "			</ul>";
		$paginas .= "		</nav>";
		$paginas .= "	</div>";
		$paginas .= "</div>";
		
		return $paginas;
	}
}