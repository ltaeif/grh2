<?php

	// Select accounts
	//$response = mysql_query("SELECT idpersonnel as record_id, parentid, nom_arabe, prenom_arabe, tel, email, image FROM my_personnel") or die(mysql_error());

		$sql= "SELECT p.idpersonnel as record_id, parentid,  g.intitule, p.nom_arabe, p.prenom_arabe, p.tel, p.email, p.image
FROM my_personnel p
INNER JOIN my_grade_lang g ON p.idgrade=g.grade_idgrade where g.lang_idlang=1";
	$q = $pdo->prepare($sql);


	// create some class for your records
	class Account
	{
		public $id = 0;
		public $parent = null;

		public $nom_arabe = '';
		public $prenom_arabe = '';
		public $intitule = '';
		public $phone = '';
		public $email = '';
		public $image = '';

		public function load($record) {
			$this->id = $record['record_id'];
			$this->parent = $record['parentid'];

			$this->nom_arabe = $record['nom_arabe'];
			$this->prenom_arabe = $record['prenom_arabe'];
			$this->intitule = $record['intitule'];
			$this->tel = $record['tel'];
			$this->email = $record['email'];
			$this->image = "demo/images/photos/" . $record['image'];
			$this->href = "showdetails.php?recordid=" . $this->id;
		}
	}

	// create hash and group all children by parentid
	$items = Array();
	foreach($pdo->query($sql) as $record )
	{
		$account = new Account();
		$account->load($record);
		array_push($items, $account);
	}

	function encodeURIComponent($str) {
        $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
        return strtr(rawurlencode($str), $revert);
    }

	// serialize $rootAccount object including all its children into JSON string
	$jsonstring = encodeURIComponent(json_encode($items));
?>


	<link rel="stylesheet" href="demo/js/jquery/ui-lightness/jquery-ui-1.10.2.custom.css" />
	<script type="text/javascript" src="demo/js/jquery/jquery-1.9.1.js"></script>
	<script type="text/javascript" src="demo/js/jquery/jquery-ui-1.10.2.custom.js"></script>

	<script type="text/javascript" src="demo/js/json3.min.js"></script>

	<!-- jQuery UI Layout -->
	<script type="text/javascript" src="demo/jquerylayout/jquery.layout-latest.min.js"></script>
	<link rel="stylesheet" type="text/css" href="demo/jquerylayout/layout-default-latest.css" />

	<script type="text/javascript">
		jQuery(document).ready(function () {
			jQuery('body').layout(
			{
				center__paneSelector: "#contentpanel"
				, north__paneSelector: "#northpanel"
				, north__resizable: false
				, north__closable: false
				, north__spacing_open: 0
				, north__size: 40
			});
		});
	</script>

	<!-- # include file="src/src.primitives.html"-->

	<!-- header -->
	<link href="demo/css/primitives.latest.css?2023" media="screen" rel="stylesheet" type="text/css" />
	<script  type="text/javascript" src="demo/js/primitives.min.js?2023"></script>


	<script type="text/javascript">
		/* insert serialized JSON string here */
		<?php Print("var data=\"" . $jsonstring . "\";"); ?>
	</script>

	<script type="text/javascript">
		var orgDiagram = null;

		jQuery(document).ready(function () {
			jQuery.ajaxSetup({
				cache: false
			});

			jQuery('#contentpanel').layout(
			{
				center__paneSelector: "#centerpanel"
				, south__paneSelector: "#southpanel"
				, south__resizable: false
				, south__closable: false
				, south__spacing_open: 0
				, south__size: 50
				, west__size: 200
				, west__paneSelector: "#westpanel"
				, west__resizable: true
				, center__onresize: function () {
					if (orgDiagram != null) {
						jQuery("#centerpanel").orgDiagram("update", primitives.common.UpdateMode.Refresh);
					}
				}
			});

			var templates = [];
			templates.push(getContactTemplate());

			var items = JSON.parse(decodeURIComponent(data));
			items[0].templateName = "contactTemplate";

			orgDiagram = jQuery("#centerpanel").orgDiagram({
				items: items,
				cursorItem: 2,
				graphicsType: primitives.common.GraphicsType.SVG,
				pageFitMode: primitives.common.PageFitMode.FitToPage,
				verticalAlignment: primitives.common.VerticalAlignmentType.Middle,
				connectorType: primitives.common.ConnectorType.Angular,
				minimalVisibility: primitives.common.Visibility.Dot,
				selectionPathMode: primitives.common.SelectionPathMode.FullStack,
				leavesPlacementType: primitives.common.ChildrenPlacementType.Horizontal,
				hasButtons: primitives.common.Enabled.False,
				hasSelectorCheckbox: primitives.common.Enabled.False,
				templates: templates,
				onButtonClick: onButtonClick,
				onCursorChanging: onCursorChanging,
				onCursorChanged: onCursorChanged,
				onHighlightChanging: onHighlightChanging,
				onHighlightChanged: onHighlightChanged,
				onSelectionChanged: onSelectionChanged,
				onItemRender: onTemplateRender,
				itemTitleFirstFontColor: primitives.common.Colors.White,
				itemTitleSecondFontColor: primitives.common.Colors.White
			});
		});

		function getContactTemplate() {
			var result = new primitives.orgdiagram.TemplateConfig();
			result.name = "contactTemplate";

			result.itemSize = new primitives.common.Size(220, 120);
			result.minimizedItemSize = new primitives.common.Size(3, 3);
			result.highlightPadding = new primitives.common.Thickness(2, 2, 2, 2);


			var itemTemplate = jQuery(
			  '<div class="bp-item bp-corner-all bt-item-frame">'
				+ '<div class="bp-item bp-corner-all bp-title-frame" style="top: 2px; left: 2px; width: 216px; height: 20px;">'
					+ '<div class="bp-item bp-title" style="top: 3px; left: 6px; width: 208px; height: 18px;">'

					+ '<span name="prenom_arabe">' +'</span>&nbsp;'
					+ '<span name="nom_arabe" >'+'</span>'

					+ '</div>'
				+ '</div>'
				+ '<div class="bp-item bp-photo-frame" style="top: 26px; left: 2px; width: 50px; height: 60px;">'
					+ '<img name="image" style="height:60px; width:50px;" />'
				+ '</div>'
				+ '<div name="tel" class="bp-item" style="top: 26px; left: 56px; width: 162px; height: 18px; font-size: 12px;"></div>'
				+ '<div name="email" class="bp-item" style="top: 44px; left: 56px; width: 162px; height: 18px; font-size: 12px;"></div>'
				+ '<div name="intitule" class="bp-item" style="top: 62px; left: 56px; width: 162px; height: 36px; font-size: 10px;"></div>'
				+ '<a name="readmore" class="bp-item" style="top: 104px; left: 4px; width: 212px; height: 12px; font-size: 10px; font-family: Arial; text-align: right; font-weight: bold; text-decoration: none; z-index:100;">Read more ...</a>'
			+ '</div>'
			).css({
				width: result.itemSize.width + "px",
				height: result.itemSize.height + "px"
			}).addClass("bp-item bp-corner-all bt-item-frame");
			result.itemTemplate = itemTemplate.wrap('<div>').parent().html();

			return result;
		}

		function onTemplateRender(event, data) {
			var hrefElement = data.element.find("[name=readmore]");
			switch (data.renderingMode) {
				case primitives.common.RenderingMode.Create:
					/* Initialize widgets here */
					hrefElement.click(function (e)
					{
						/* Block mouse click propogation in order to avoid layout updates before server postback*/
						primitives.common.stopPropagation(e);
					});
					break;
				case primitives.common.RenderingMode.Update:
					/* Update widgets here */
					break;
			}

			var itemConfig = data.context;

			if (data.templateName == "contactTemplate") {
				data.element.find("[name=image]").attr({ "src": itemConfig.image });

				var fields = ["nom_arabe", "prenom_arabe", "tel", "email", "intitule"];
				for (var index = 0; index < fields.length; index += 1) {
					var field = fields[index];

					var element = data.element.find("[name=" + field + "]");
					if (element.text() != itemConfig[field]) {
						element.text(itemConfig[field]);
					}
				}
			}
			hrefElement.attr({ "href": itemConfig.href });
		}

		function onSelectionChanged(e, data) {
			var selectedItems = jQuery("#centerpanel").orgDiagram("option", "selectedItems");
			var message = "";
			for (var index = 0; index < selectedItems.length; index += 1) {
				var itemConfig = selectedItems[index];
				if (message != "") {
					message += ", ";
				}
				message += "<b>'" + itemConfig.nom_arabe + "'</b>";
			}
			message += (data.parentItem != null ? " Parent item <b>'" + data.parentItem.nom_arabe + "'" : "");
			jQuery("#southpanel").empty().append("User selected following items: " + message);
		}

		function onHighlightChanging(e, data) {
			var message = (data.context != null) ? "User is hovering mouse over item <b>'" + data.context.nom_arabe + "'</b>." : "";
			message += (data.parentItem != null ? " Parent item <b>'" + data.parentItem.nom_arabe + "'" : "");

			jQuery("#southpanel").empty().append(message);
		}

		function onHighlightChanged(e, data) {
			var message = (data.context != null) ? "User hovers mouse over item <b>'" + data.context.nom_arabe + "'</b>." : "";
			message += (data.parentItem != null ? " Parent item <b>'" + data.parentItem.nom_arabe + "'" : "");

			jQuery("#southpanel").empty().append(message);
		}

		function onCursorChanging(e, data) {
			var message = "User is clicking on item '" + data.context.nom_arabe + "'.";
			message += (data.parentItem != null ? " Parent item <b>'" + data.parentItem.nom_arabe + "'" : "");

			jQuery("#southpanel").empty().append(message);

			data.oldContext.templateName = null;
			data.context.templateName = "contactTemplate";
		}

		function onCursorChanged(e, data) {
			var message = "User clicked on item '" + data.context.nom_arabe + "'.";
			message += (data.parentItem != null ? " Parent item <b>'" + data.parentItem.nom_arabe + "'" : "");
			jQuery("#southpanel").empty().append(message);
		}

		function onButtonClick(e, data) {
			var message = "User clicked <b>'" + data.name + "'</b> button for item <b>'" + data.context.nom_arabe + "'</b>.";
			message += (data.parentItem != null ? " Parent item <b>'" + data.parentItem.nom_arabe + "'" : "");
			jQuery("#southpanel").empty().append(message);
		}

	</script>


<div style="font-size:12px">
	<div id="contentpanel" style="padding: 0px;">
		<!--
        <div id="westpanel" style="padding: 5px; margin: 0px; border-style: solid; font-size: 12px; border-color: grey; border-width: 1px; overflow: scroll;">
			<h2>Organigramme Université virtuelle de Tunis</h2>
			<p><a href="personnel.php" />Gestion des personnels</a></p>
			<p></p>
			<p></p>
		</div>
		-->
		<div id="centerpanel" style="overflow: hidden; padding: 0px; margin: 0px; border: 0px;">
		</div>
		<div id="southpanel">
		</div>
	</div>

    <!--
    <div id="northpanel" style="padding: 0px; margin: 0px;">
		<h2>Organigramme Université virtuelle de Tunis</h2>
	</div>
	-->
</div>
