<?php
/**
 * 이 파일은 iModule 팝업모듈 일부입니다. (https://www.imodules.io)
 *
 * 팝업모듈 관리자 UI를 구성한다.
 * 
 * @file /modules/popup/admin/index.php
 * @author Arzz (arzz@arzz.com)
 * @license GPLv3
 * @version 3.1.0
 * @modified 2019. 9. 19.
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
				iconCls:"xi xi-windows",
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
							Popup.list.add();
						}
					}),
					"-",
					new Ext.Button({
						text:"선택팝업삭제",
						iconCls:"mi mi-trash",
						handler:function() {
							Popup.list.delete();
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
					sorters:[{property:"title",direction:"ASC"}],
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
						Popup.list.add(record.data.idx);
					},
					itemcontextmenu:function(grid,record,item,index,e) {
						var menu = new Ext.menu.Menu();
						
						menu.addTitle(record.data.title);
						
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
								Popup.list.add(record.data.idx);
							}
						});
						
						menu.add({
							iconCls:"mi mi-trash",
							text:"팝업 삭제",
							handler:function() {
								Popup.list.delete(record.data.idx);
							}
						});
						
						e.stopEvent();
						menu.showAt(e.getXY());
					}
				}
			}),
			<?php if ($this->IM->getModule('member')->isAdmin() == true) { ?>
			new Ext.grid.Panel({
				id:"ModulePopupAdminList",
				iconCls:"xi xi-crown",
				title:"관리자 관리",
				border:false,
				tbar:[
					new Ext.Button({
						text:"관리자 추가",
						iconCls:"mi mi-plus",
						handler:function() {
							Popup.admin.add();
						}
					}),
					new Ext.Button({
						text:"선택관리자 삭제",
						iconCls:"mi mi-trash",
						handler:function() {
							var selected = Ext.getCmp("ModulePopupAdminList").getSelectionModel().getSelection();
							if (selected.length == 0) {
								Ext.Msg.show({title:Admin.getText("alert/error"),msg:"삭제할 관리자를 선택하여 주십시오.",buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
								return;
							}
							
							var midxes = [];
							for (var i=0, loop=selected.length;i<loop;i++) {
								midxes[i] = selected[i].data.midx;
							}
							Popup.admin.delete(midxes.join(','));
						}
					})
				],
				store:new Ext.data.JsonStore({
					proxy:{
						type:"ajax",
						simpleSortMode:true,
						url:ENV.getProcessUrl("popup","@getAdmins"),
						extraParams:{},
						reader:{type:"json"}
					},
					remoteSort:false,
					sorters:[{property:"sort",direction:"ASC"}],
					autoLoad:true,
					pageSize:0,
					fields:[],
					listeners:{
						load:function(store,records,success,e) {
							if (success == false) {
								if (e.getError()) {
									Ext.Msg.show({title:Admin.getText("alert/error"),msg:e.getError(),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
								} else {
									Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("LOAD_DATA_FAILED"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
								}
							}
						}
					}
				}),
				columns:[{
					text:"이름",
					dataIndex:"name",
					sortable:true,
					width:80,
					renderer:function(value,p,record) {
						var sHTML = "";
						if (record.data.team && record.data.role == "OWNER") sHTML+= '<i class="xi xi-crown"></i> ';
						sHTML+= value;
						
						return sHTML;
					}
				},{
					text:"이메일",
					dataIndex:"email",
					sortable:true,
					width:180
				},{
					text:"접근가능 사이트",
					dataIndex:"site",
					sortable:true,
					minWidth:140,
					flex:1
				}],
				selModel:new Ext.selection.CheckboxModel(),
				bbar:[
					new Ext.Button({
						iconCls:"x-tbar-loading",
						handler:function() {
							Ext.getCmp("ModulePopupAdminList").getStore().reload();
						}
					}),
					"->",
					{xtype:"tbtext",text:Admin.getText("text/grid_help")}
				],
				listeners:{
					itemdblclick:function(grid,record) {
						Popup.admin.add(record.data.midx);
					},
					itemcontextmenu:function(grid,record,item,index,e) {
						var menu = new Ext.menu.Menu();
						
						menu.addTitle(record.data.name);
						
						menu.add({
							iconCls:"xi xi-key",
							text:"접근권한수정",
							handler:function() {
								Popup.admin.add(record.data.midx);
							}
						});

						menu.add({
							iconCls:"xi xi-trash",
							text:"삭제",
							handler:function() {
								Popup.admin.delete(record.data.midx);
							}
						});
						
						e.stopEvent();
						menu.showAt(e.getXY());
					}
				}
			}),
			<?php } ?>
			null
		]
	})
); });
</script>