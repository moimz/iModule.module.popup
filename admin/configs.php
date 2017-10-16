<?php
/**
 * 이 파일은 iModule CTL 모듈의 일부입니다. (https://www.imodule.kr)
 *
 * 팝업모듈 환경설정 패널을 가져온다.
 * 
 * @file /modules/popup/admin/configs.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0.160910
 */
if (defined('__IM__') == false) exit;
?>
<script>
new Ext.form.Panel({
	id:"ModuleConfigForm",
	border:false,
	bodyPadding:"10 10 5 10",
	fieldDefaults:{labelAlign:"right",labelWidth:100,anchor:"100%",allowBlank:true},
	items:[
		new Ext.form.FieldSet({
			title:Popup.getText("admin/configs/form/default_setting"),
			items:[
				Admin.templetField(Popup.getText("admin/configs/form/templet"),"templet","popup",false),
				new Ext.form.Checkbox({
					name:"auto_layer",
					fieldLabel:"자동레이어팝업",
					uncheckedValue:"",
					boxLabel:"팝업이 차단되었을 경우 자동으로 레이어팝업으로 팝업을 띄웁니다."
				})
			]
		}),
		new Ext.form.FieldSet({
			title:"팝업 시작위치",
			items:[
				new Ext.form.FieldContainer({
					fieldLabel:"팝업창시작위치",
					layout:"hbox",
					items:[
						new Ext.form.NumberField({
							name:"window_x",
							width:130,
							value:0,
							minValue:0,
							maxValue:1000
						}),
						new Ext.form.DisplayField({
							value:"X",
							style:{marginLeft:"5px",marginRight:"5px"}
						}),
						new Ext.form.NumberField({
							name:"window_y",
							width:130,
							value:0,
							minValue:0,
							maxValue:1000
						})
					],
					afterBodyEl:'<div class="x-form-help">팝업 윈도우의 시작위치의 X,Y 좌표를 설정합니다.</div>'
				}),
				new Ext.form.FieldContainer({
					fieldLabel:"레이어시작위치",
					layout:"hbox",
					items:[
						new Ext.form.NumberField({
							name:"layer_x",
							width:130,
							value:0,
							minValue:0,
							maxValue:1000
						}),
						new Ext.form.DisplayField({
							value:"X",
							style:{marginLeft:"5px",marginRight:"5px"}
						}),
						new Ext.form.NumberField({
							name:"layer_y",
							width:130,
							value:0,
							minValue:0,
							maxValue:1000
						})
					],
					afterBodyEl:'<div class="x-form-help">팝업 레이어의 시작위치의 X,Y 좌표를 설정합니다.</div>'
				})
			]
		})
	]
});
</script>