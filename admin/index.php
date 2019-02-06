<?php
/**
 * 이 파일은 iModule 팝업모듈 일부입니다. (https://www.imodules.io)
 *
 * 팝업모듈 관리자 UI를 구성한다.
 * 
 * @file /modules/popup/admin/index.php
 * @author Arzz (arzz@arzz.com)
 * @license GPLv3
 * @version 3.0.0
 * @modified 2019. 2. 6.
 */
if (defined('__IM__') == false) exit;
?>
<script>
Ext.onReady(function () { Ext.getCmp("iModuleAdminPanel").add(
	new Ext.TabPanel({
		id:"ModulePopup",
		border:false,
		tabPosition:"bottom",
		items:[
			new Ext.grid.Panel({
				id:"ModulePopupList",
				title:"팝업관리",
				border:false,
				layout:"fit",
				tbar:[
					new Ext.form.ComboBox({
						id:"SiteList",
						store:new Ext.data.JsonStore({
							proxy:{
								type:"ajax",
								simpleSortMode:true,
								url:ENV.getProcessUrl("admin","@getSites"),
								extraParams:{is_all:"true"},
								reader:{type:"json"}
							},
							remoteSort:false,
							sorters:[{property:"sort",direction:"ASC"}],
							autoLoad:true,
							pageSize:0,
							fields:["display","value"],
							listeners:{
								load:function(store,records,success,e) {
									if (success == true) {
										if (store.getCount() > 0 && store.findExact("value",Ext.getCmp("SiteList").getValue(),0) == -1) {
											Ext.getCmp("SiteList").setValue(store.getAt(0).get("value"));
										}
									} else {
										if (e.getError()) {
											Ext.Msg.show({title:Admin.getText("alert/error"),msg:e.getError(),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
										} else {
											Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("DATA_LOAD_FAILED"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
										}
									}
								}
							}
						}),
						autoLoadOnValue:true,
						editable:false,
						displayField:"display",
						valueField:"value",
						width:400,
						listeners:{
							change:function(form,value) {
								if (value) {
									var temp = value.split("@");
									var domain = temp[0];
									var language = temp[1];
									Ext.getCmp("ModulePopupList").getStore().getProxy().setExtraParam("domain",domain);
									Ext.getCmp("ModulePopupList").getStore().getProxy().setExtraParam("language",language);
									Ext.getCmp("ModulePopupList").getStore().reload();
								}
							}
						}
					}),
					new Ext.Button({
						text:"팝업생성",
						iconCls:"xi xi-windows-add",
						handler:function() {
							Popup.add();
						}
					}),
					"-",
					new Ext.Button({
						text:"선택팝업삭제",
						iconCls:"mi mi-trash",
						handler:function() {
							Popup.delete();
						}
					})
				],
				store:new Ext.data.JsonStore({
					proxy:{
						type:"ajax",
						simpleSortMode:true,
						url:ENV.getProcessUrl("popup","@getPopups"),
						extraParams:{type:"",keyword:""},
						reader:{type:"json"}
					},
					remoteSort:true,
					sorters:[{property:"start_date",direction:"DESC"}],
					autoLoad:true,
					pageSize:50,
					fields:[],
					listeners:{
						load:function(store,records,success,e) {
							if (success == false) {
								if (e.getError()) {
									Ext.Msg.show({title:Admin.getText("alert/error"),msg:e.getError(),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
								} else {
									Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getText("error/load"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
								}
							}
						}
					}
				}),
				columns:[{
					text:"게시사이트",
					dataIndex:"site",
					minWidth:150,
					flex:1
				},{
					text:"팝업명",
					dataIndex:"title",
					width:180
				},{
					text:"종류",
					dataIndex:"type",
					width:120,
					align:"center",
					renderer:function(value) {
						return Popup.getText("type/"+value);
					}
				},{
					text:"팝업형태",
					dataIndex:"window",
					width:80,
					align:"center",
					renderer:function(value) {
						return Popup.getText("window/"+value);
					}
				},{
					text:"크기",
					dataIndex:"size",
					width:100,
					align:"center",
					renderer:function(value) {
						var temp = value.split(",");
						return temp[0] + " x " + temp[1] + "px";
					}
				},{
					text:"게시시작일",
					dataIndex:"start_date",
					width:120,
					align:"center",
					renderer:function(value) {
						return moment(value * 1000).locale($("html").attr("lang")).format("YYYY-MM-DD(dd)");
					}
				},{
					text:"게시종료일",
					dataIndex:"end_date",
					width:120,
					align:"center",
					renderer:function(value) {
						return moment(value * 1000).locale($("html").attr("lang")).format("YYYY-MM-DD(dd)");
					}
				},{
					text:"등록일",
					dataIndex:"reg_date",
					width:140,
					align:"center",
					renderer:function(value) {
						return moment(value * 1000).locale($("html").attr("lang")).format("YYYY-MM-DD HH:mm");
					}
				}],
				selModel:new Ext.selection.CheckboxModel(),
				bbar:new Ext.PagingToolbar({
					store:null,
					displayInfo:false,
					items:[
						"->",
						{xtype:"tbtext",text:Admin.getText("text/grid_help")}
					],
					listeners:{
						beforerender:function(tool) {
							tool.bindStore(Ext.getCmp("ModulePopupList").getStore());
						}
					}
				}),
				listeners:{
					itemdblclick:function(grid,record) {
						Popup.add(record.data.idx);
					},
					itemcontextmenu:function(grid,record,item,index,e) {
						var menu = new Ext.menu.Menu();
						
						menu.add('<div class="x-menu-title">'+record.data.title+'</div>');
						
						menu.add({
							iconCls:"xi xi-windows",
							text:"팝업 미리보기",
							handler:function() {
								var temp = record.data.size.split(",");
								iModule.openPopup(ENV.getModuleUrl("popup","window",record.data.idx),temp[0],temp[1],0);
							}
						});
						
						menu.add("-");
						
						menu.add({
							iconCls:"xi xi-form",
							text:"팝업 수정",
							handler:function() {
								Popup.add(record.data.idx);
							}
						});
						
						menu.add({
							iconCls:"mi mi-trash",
							text:"팝업 삭제",
							handler:function() {
								Popup.delete(record.data.idx);
							}
						});
						
						e.stopEvent();
						menu.showAt(e.getXY());
					}
				}
			})
		]
	})
); });
</script>