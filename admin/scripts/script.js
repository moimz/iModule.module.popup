/**
 * 이 파일은 iModule 팝업모듈 일부입니다. (https://www.imodule.kr)
 *
 * 팝업모듈 관리자 UI를 구성한다.
 * 
 * @file /modules/popup/admin/scripts/script.js
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0.160903
 */
var Popup = {
	add:function(idx) {
		new Ext.Window({
			id:"ModulePopupAddWindow",
			title:(idx ? "팝업수정" : "팝업생성"),
			width:700,
			modal:true,
			autoScroll:true,
			border:false,
			items:[
				new Ext.form.Panel({
					id:"ModulePopupAddForm",
					border:false,
					bodyPadding:"10 10 10 10",
					fieldDefaults:{labelAlign:"right",labelWidth:100,anchor:"100%",allowBlank:false},
					items:[
						new Ext.form.Hidden({
							name:"idx"
						}),
						new Ext.form.FieldSet({
							title:"기본설정",
							items:[
								new Ext.form.TextField({
									name:"title",
									fieldLabel:"팝업명"
								}),
								new Ext.form.FieldContainer({
									fieldLabel:"팝업게시일",
									layout:"hbox",
									items:[
										new Ext.form.DateField({
											name:"start_date",
											format:"Y-m-d",
											width:130,
											value:moment().format("YYYY-MM-DD")
										}),
										new Ext.form.DisplayField({
											value:"~",
											style:{marginLeft:"5px",marginRight:"5px"}
										}),
										new Ext.form.DateField({
											name:"end_date",
											format:"Y-m-d",
											width:130,
											value:moment().add(1,"w").format("YYYY-MM-DD")
										}),
										new Ext.form.DisplayField({
											flex:1
										}),
										new Ext.Button({
											text:"1일",
											style:{marginLeft:"2px"},
											handler:function() {
												var start_date = moment(Ext.getCmp("ModulePopupAddForm").getForm().findField("start_date").getValue());
												Ext.getCmp("ModulePopupAddForm").getForm().findField("end_date").setValue(start_date.add(1,"day").format("YYYY-MM-DD"));
											}
										}),
										new Ext.Button({
											text:"3일",
											style:{marginLeft:"2px"},
											handler:function() {
												var start_date = moment(Ext.getCmp("ModulePopupAddForm").getForm().findField("start_date").getValue());
												Ext.getCmp("ModulePopupAddForm").getForm().findField("end_date").setValue(start_date.add(3,"day").format("YYYY-MM-DD"));
											}
										}),
										new Ext.Button({
											text:"7일",
											style:{marginLeft:"2px"},
											handler:function() {
												var start_date = moment(Ext.getCmp("ModulePopupAddForm").getForm().findField("start_date").getValue());
												Ext.getCmp("ModulePopupAddForm").getForm().findField("end_date").setValue(start_date.add(7,"day").format("YYYY-MM-DD"));
											}
										}),
										new Ext.Button({
											text:"15일",
											style:{marginLeft:"2px"},
											handler:function() {
												var start_date = moment(Ext.getCmp("ModulePopupAddForm").getForm().findField("start_date").getValue());
												Ext.getCmp("ModulePopupAddForm").getForm().findField("end_date").setValue(start_date.add(15,"day").format("YYYY-MM-DD"));
											}
										}),
										new Ext.Button({
											text:"30일",
											style:{marginLeft:"2px"},
											handler:function() {
												var start_date = moment(Ext.getCmp("ModulePopupAddForm").getForm().findField("start_date").getValue());
												Ext.getCmp("ModulePopupAddForm").getForm().findField("end_date").setValue(start_date.add(30,"day").format("YYYY-MM-DD"));
											}
										})
									]
								}),
								new Ext.form.ComboBox({
									fieldLabel:"팝업종류",
									name:"type",
									store:new Ext.data.ArrayStore({
										fields:["display","value"],
										data:[["내용 직접입력","HTML"],["이미지","IMAGE"],["외부 URL","LINK"],["외부 파일","EXTERNAL"]]
									}),
									editable:false,
									displayField:"display",
									valueField:"value",
									value:"HTML",
									listeners:{
										change:function(form,value) {
											Ext.getCmp("ModulePopupAdd-HTML").disable();
											Ext.getCmp("ModulePopupAdd-HTML").hide();
											
											Ext.getCmp("ModulePopupAdd-"+value).enable();
											Ext.getCmp("ModulePopupAdd-"+value).show();
											
											if (value == "IMAGE") {
												Ext.getCmp("ModulePopupAddSize").disable();
												Ext.getCmp("ModulePopupAddSize").hide();
											} else {
												Ext.getCmp("ModulePopupAddSize").enable();
												Ext.getCmp("ModulePopupAddSize").show();
											}
										}
									}
								}),
								new Ext.form.ComboBox({
									fieldLabel:"팝업형태",
									name:"window",
									store:new Ext.data.ArrayStore({
										fields:["display","value"],
										data:[["팝업창","WINDOW"],["레이어팝업","LAYER"]]
									}),
									editable:false,
									displayField:"display",
									valueField:"value",
									value:"WINDOW"
								})
							]
						}),
						new Ext.form.FieldSet({
							id:"ModulePopupAdd-HTML",
							title:"팝업내용입력",
							items:[
								Admin.wysiwygField("","html","")
							]
						}),
						new Ext.form.FieldSet({
							id:"ModulePopupAdd-IMAGE",
							title:"이미지설정",
							disabled:true,
							hidden:true,
							items:[
								new Ext.form.FileUploadField({
									fieldLabel:"팝업이미지",
									name:"image",
									buttonText:"찾아보기",
									accept:"image/*",
									allowBlank:idx !== undefined,
									afterBodyEl:'<div id="ModulePopupAddImageHelp" class="x-form-help">첨부된 이미지크기에 맞게 팝업사이즈가 자동으로 결정됩니다.</div>'
								}),
								new Ext.form.TextField({
									fieldLabel:"이동 URL",
									name:"image_link",
									allowBlank:true,
									afterBodyEl:'<div class="x-form-help">이미지클릭시 이동할 URL이 있다면 URL주소를 입력하여 주십시오.</div>'
								})
							]
						}),
						new Ext.form.FieldSet({
							id:"ModulePopupAdd-LINK",
							title:"외부 URL 설정",
							hidden:true,
							disabled:true,
							items:[
								new Ext.form.TextField({
									fieldLabel:"외부 URL",
									name:"link",
									afterBodyEl:'<div class="x-form-help">팝업창에 표시될 URL 을 입력하여 주십시오.</div>'
								})
							]
						}),
						new Ext.form.FieldSet({
							id:"ModulePopupAdd-EXTERNAL",
							title:"외부파일설정",
							hidden:true,
							disabled:true,
							items:[
								new Ext.form.ComboBox({
									name:"external",
									store:new Ext.data.JsonStore({
										proxy:{
											type:"ajax",
											url:ENV.getProcessUrl("admin","@getExternals"),
											reader:{type:"json"}
										},
										autoLoad:true,
										remoteSort:false,
										sorters:[{property:"path",direction:"ASC"}],
										fields:["path"]
									}),
									displayField:"path",
									valueField:"path",
									afterBodyEl:'<div class="x-form-help">팝업창에 표시될 외부파일을 선택하여 주십시오.</div>'
								}),
							]
						}),
						new Ext.form.FieldSet({
							title:"팝업크기/위치 설정",
							items:[
								new Ext.form.FieldContainer({
									id:"ModulePopupAddSize",
									fieldLabel:"크기(가로x세로)",
									layout:"hbox",
									items:[
										new Ext.form.NumberField({
											name:"width",
											width:130,
											value:400,
											minValue:0,
											maxValue:1000
										}),
										new Ext.form.DisplayField({
											value:"X",
											style:{marginLeft:"5px",marginRight:"5px"}
										}),
										new Ext.form.NumberField({
											name:"height",
											width:130,
											value:400,
											minValue:0,
											maxValue:1000
										})
									]
								}),
								new Ext.form.FieldContainer({
									id:"ModulePopupAddPosition",
									fieldLabel:"위치(X,Y)",
									layout:"hbox",
									items:[
										new Ext.form.NumberField({
											name:"x",
											width:130,
											value:0,
											minValue:0,
											maxValue:1000,
											disabled:true
										}),
										new Ext.form.DisplayField({
											value:"X",
											style:{marginLeft:"5px",marginRight:"5px"}
										}),
										new Ext.form.NumberField({
											name:"y",
											width:130,
											value:0,
											minValue:0,
											maxValue:1000,
											disabled:true
										}),
										new Ext.form.DisplayField({
											flex:1
										}),
										new Ext.form.Checkbox({
											boxLabel:"자동설정",
											name:"auto_position",
											checked:true,
											listeners:{
												change:function(form,value) {
													form.getForm().findField("x").setDisabled(value);
													form.getForm().findField("y").setDisabled(value);
												}
											}
										})
									],
									afterBodyEl:'<div class="x-form-help">팝업창의 위치를 모니터 좌상단 기준으로 지정합니다.<br>자동으로 설정할 경우 가급적 팝업이 겹쳐지지 않는 범위내에서 자동으로 위치를 결정합니다.</div>'
								})
							]
						})
					]
				})
			],
			buttons:[
				new Ext.Button({
					text:Admin.getText("button/confirm"),
					handler:function() {
						Ext.getCmp("ModulePopupAddForm").getForm().submit({
							url:ENV.getProcessUrl("popup","@savePopup"),
							submitEmptyText:false,
							waitTitle:Admin.getText("action/wait"),
							waitMsg:Admin.getText("action/saving"),
							success:function(form,action) {
								Ext.Msg.show({title:Admin.getText("alert/info"),msg:Admin.getText("action/saved"),buttons:Ext.Msg.OK,icon:Ext.Msg.INFO,fn:function() {
									Ext.getCmp("ModulePopupList").getStore().loadPage(1);
									Ext.getCmp("ModulePopupAddWindow").close();
								}});
							},
							failure:function(form,action) {
								if (action.result) {
									if (action.result.message) {
										Ext.Msg.show({title:Admin.getText("alert/error"),msg:action.result.message,buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
									} else {
										Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("DATA_SAVE_FAILED"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
									}
								} else {
									Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("INVALID_FORM_DATA"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
								}
							}
						});
					}
				}),
				new Ext.Button({
					text:Admin.getText("button/cancel"),
					handler:function() {
						Ext.getCmp("ModulePopupAddWindow").close();
					}
				})
			],
			listeners:{
				show:function() {
					if (idx) {
						Ext.getCmp("ModulePopupAddForm").getForm().load({
							url:ENV.getProcessUrl("popup","@getPopup"),
							params:{idx:idx},
							waitTitle:Admin.getText("action/wait"),
							waitMsg:Admin.getText("action/loading"),
							success:function(form,action) {
								if (action.result.data.type == "IMAGE") {
									$("#ModulePopupAddImageHelp").html("기존에 첨부된 [" + action.result.data.image.name + "] 이미지를 변경하시려면 변경할 파일을 선택하여 주십시오.");
								}
							},
							failure:function(form,action) {
								if (action.result && action.result.message) {
									Ext.Msg.show({title:Admin.getText("alert/error"),msg:action.result.message,buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
								} else {
									Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("DATA_LOAD_FAILED"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
								}
								Ext.getCmp("ModulePopupAddWindow").close();
							}
						});
					}
				}
			}
		}).show();
	},
	delete:function() {
		var selected = Ext.getCmp("ModulePopupList").getSelectionModel().getSelection();
		if (selected.length == 0) {
			Ext.Msg.show({title:Admin.getText("alert/error"),msg:"삭제할 팝업을 선택하여 주십시오.",buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
			return;
		}
		
		var idxes = [];
		for (var i=0, loop=selected.length;i<loop;i++) {
			idxes.push(selected[i].get("idx"));
		}
		
		Ext.Msg.show({title:Admin.getText("alert/info"),msg:"선택한 팝업을 삭제하시겠습니까?",buttons:Ext.Msg.OKCANCEL,icon:Ext.Msg.QUESTION,fn:function(button) {
			if (button == "ok") {
				Ext.Msg.wait(Admin.getText("action/working"),Admin.getText("action/wait"));
				$.send(ENV.getProcessUrl("popup","@deletePopup"),{idx:idxes.join(",")},function(result) {
					if (result.success == true) {
						Ext.Msg.show({title:Admin.getText("alert/info"),msg:Admin.getText("action/worked"),buttons:Ext.Msg.OK,icon:Ext.Msg.INFO,fn:function() {
							Ext.getCmp("ModulePopupList").getStore().reload();
						}});
					}
				});
			}
		}});
	}
};