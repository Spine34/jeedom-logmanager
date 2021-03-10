<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('logmanager');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
	<div class="col-xs-12 eqLogicThumbnailDisplay">
	<legend><i class="fas fa-cog"></i>  {{Gestion}}</legend>
	<div class="eqLogicThumbnailContainer">
		<div class="cursor eqLogicAction logoSecondary" data-action="add">
			<i class="fas fa-plus-circle"></i>
			<br>
			<span>{{Ajouter}}</span>
		</div>
		<div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
			<i class="fas fa-wrench"></i>
			<br>
			<span>{{Configuration}}</span>
		</div>
		<div class="cursor pluginAction logoSecondary" data-action="openLocation" data-location="<?=$plugin->getDocumentation()?>">
			<i class="fas fa-book"></i>
			<br>
			<span>{{Documentation}}</span>
		</div>
		<div class="cursor pluginAction logoSecondary" data-action="openLocation" data-location="https://community.jeedom.com/tags/plugin-<?=$plugin->getId()?>">
			<i class="fas fa-comments"></i>
			<br>
			<span>Community</span>
		</div>
	</div>
	<legend><i class="fas fa-table"></i> {{Mes Logs}}</legend>
	<input class="form-control" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
	<div class="eqLogicThumbnailContainer">
		<?php
		foreach ($eqLogics as $eqLogic) {
			$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
			echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
			echo '<img src="' . $eqLogic->getImage() . '"/>';
			echo "<br>";
			echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
			echo '</div>';
		}
		?>
	</div>
</div>

<div class="col-xs-12 eqLogic" style="display: none;">
	<div class="input-group pull-right" style="display:inline-flex">
		<span class="input-group-btn">
			<a class="btn btn-default btn-sm eqLogicAction roundedLeft" data-action="configure"><i class="fas fa-cogs"></i> {{Configuration avancée}}</a><a class="btn btn-default btn-sm eqLogicAction" data-action="copy"><i class="fas fa-copy"></i> {{Dupliquer}}<div></div></a><a class="btn btn-success btn-sm eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a><a class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
		</span>
	</div>
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
		<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
		<li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-list-alt"></i> {{Commandes}}</a></li>
	</ul>
	<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
		<div role="tabpanel" class="tab-pane active" id="eqlogictab">
			<br/>
			<div class="row">
				<div class="col-xs-12">
					<form class="form-horizontal">
						<fieldset>
							<div class="form-group">
								<label class="col-xs-2 control-label">{{Nom de l'équipement}}</label>
								<div class="col-xs-2">
									<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
									<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement}}"/>
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-2 control-label" >{{Objet parent}}</label>
								<div class="col-xs-2">
									<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
										<option value="">{{Aucun}}</option>
										<?php
											foreach (jeeObject::all() as $object) {
												echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
											}
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-2 control-label">{{Catégorie}}</label>
								<div class="col-xs-7">
									<?php
										foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
										echo '<label class="checkbox-inline">';
										echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
										echo '</label>';
										}
									?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-2 control-label"></label>
								<div class="col-xs-7">
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
								</div>
							</div>
							<br/>
							<div class="form-group">
								<label class="col-xs-2 control-label">{{Niveau de log}}</label>
								<div class="col-xs-2">
									<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="loglevel">
										<option value="100">{{Debug}}</option>
										<option value="200">{{Info}}</option>
										<option value="300">{{Warning}}</option>
										<option value="400">{{Erreur}}</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-2 control-label help" data-help="Générer un événement pour tout log écrit à partir du niveau de log sélectionné">{{Générer un événement}}</label>
								<div class="col-xs-2">
									<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="eventlevel">
										<option value="">{{Désactivé}}</option>
										<option value="100">{{Debug}}</option>
										<option value="200">{{Info}}</option>
										<option value="300">{{Warning}}</option>
										<option value="400">{{Erreur}}</option>
									</select>
								</div>
								<div class="col-xs-1">
								</div>
								<div class="col-xs-7">
									<div class="alert alert-info globalRemark">{{Génére un événement #lm-debug#, #lm-info#, #lm-warning# ou #lm-error# qui peut être utilisé comme déclencheur de scénario.}}</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-2 control-label help" data-help="Si le contenu du log est affiché, les commandes ne le seront pas.">{{Tuile}}</label>
								<div class="col-xs-3">
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="displayContentWidget"/>{{Afficher le contenu du log sur la tuile}}</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-2 control-label help">{{Nombre de lignes à afficher}}</label>
								<div class="col-xs-1">
									<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="nbrLinesWidget" placeholder="1000"/>
								</div>
							</div>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
		<div role="tabpanel" class="tab-pane" id="commandtab">
			<table id="table_cmd" class="table table-bordered table-condensed">
				<thead>
					<tr>
						<th style="width: 400px;">{{Nom}}</th>
						<th>{{Paramètres}}</th>
						<th style="width: 150px;">{{Options}}</th>
						<th style="width: 150px;">{{Actions}}</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php include_file('desktop', 'logmanager', 'js', 'logmanager');?>
<?php include_file('core', 'plugin.template', 'js');?>
