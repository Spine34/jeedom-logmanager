<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('logmanager');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
	<div class="col-xs-12 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
	<legend><i class="fas fa-cog"></i>  {{Gestion}}</legend>
	<div class="eqLogicThumbnailContainer">
		<div class="cursor eqLogicAction" data-action="add" style="text-align: center; background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
			<i class="fas fa-plus-circle" style="font-size : 6em;color:#FF7F27;"></i>
			<br>
			<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#FF7F27">{{Ajouter}}</span>
		</div>
		<div class="cursor eqLogicAction" data-action="gotoPluginConf" style="text-align: center; background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
			<i class="fas fa-wrench" style="font-size : 6em;color:#767676;"></i>
			<br>
			<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676">{{Configuration}}</span>
		</div>
		<div class="cursor pluginAction" data-action="openLocation" data-location="<?=$plugin->getDocumentation()?>" style="text-align: center; background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
			<i class="fas fa-book" style="font-size : 6em;color:#767676;"></i>
			<br>
			<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676">{{Documentation}}</span>
		</div>
	</div>
	<legend><i class="fas fa-table"></i> {{Mes Logs}}</legend>
	<input class="form-control" placeholder="{{Rechercher}}" style="margin-bottom:4px;" id="in_searchEqlogic" />
	<div class="eqLogicThumbnailContainer">
		<?php
		foreach ($eqLogics as $eqLogic) {
			$opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
			echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="text-align: center; background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;' . $opacity . '" >';
			echo '<img src="' . $eqLogic->getImage() . '" height="105" width="95" />';
			echo "<br>";
			echo '<span class="name" style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;">' . $eqLogic->getHumanName(true, true) . '</span>';
			echo '</div>';
		}
		?>
	</div>
</div>

<div class="col-xs-12 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
	<div class="input-group pull-right" style="display:inline-flex">
		<span class="input-group-btn">
			<a class="btn btn-primary btn-sm pluginAction roundedLeft" data-action="openLocation" data-location="<?=$plugin->getDocumentation()?>"><i class="fas fa-book"></i> {{Documentation}}</a>
			<a class="btn btn-default btn-sm eqLogicAction" data-action="configure"><i class="fas fa-cogs"></i> {{Configuration avancée}}</a>
			<a class="btn btn-danger btn-sm eqLogicAction" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
			<a class="btn btn-success btn-sm eqLogicAction roundedRight" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a>
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
				<div class="col-sm-7">
					<form class="form-horizontal">
						<fieldset>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Nom de l'équipement}}</label>
								<div class="col-sm-3">
									<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
									<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement}}"/>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label" >{{Objet parent}}</label>
								<div class="col-sm-3">
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
								<label class="col-sm-3 control-label">{{Catégorie}}</label>
								<div class="col-sm-9">
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
								<label class="col-sm-3 control-label"></label>
								<div class="col-sm-9">
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
								</div>
							</div>
							<br/>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Niveau de log}}</label>
								<div class="col-sm-3">
									<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="loglevel">
										<option value="100">{{Debug}}</option>
										<option value="200">{{Info}}</option>
										<option value="300">{{Warning}}</option>
										<option value="400">{{Erreur}}</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label help" data-help="Générer un événement pour tout log écrit à partir du niveau de log sélectionné">{{Générer un événement}}</label>
								<div class="col-sm-3">
									<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="eventlevel">
										<option value="">{{Désactivé}}</option>
										<option value="100">{{Debug}}</option>
										<option value="200">{{Info}}</option>
										<option value="300">{{Warning}}</option>
										<option value="400">{{Erreur}}</option>
									</select>
								</div>
							</div>
                            <div class="form-group">
                                <div class="col-sm-3">
                                </div>
                                <div class="col-sm-9">
                                    <div class="alert alert-info globalRemark" style="">{{Génére un événement #lm-debug#, #lm-info#, #lm-warning# ou #lm-error# qui peut être utilisé comme déclencheur de scénario.}}</div>
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
