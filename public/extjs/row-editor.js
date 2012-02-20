/*!
 * Ext JS Library 3.0+
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.onReady(function(){
    Ext.QuickTips.init();

    var Employee = Ext.data.Record.create([{
        name: 'name',
        type: 'string'
    }, {
        name: 'email',
        type: 'string'
    }, {
        name: 'start',
        type: 'date',
        dateFormat: 'n/j/Y'
    },{
        name: 'salary',
        type: 'float'
    },{
        name: 'active',
        type: 'bool'
    }]);


    // hideous function to generate employee data
/*    var genData = function(){
        var data = [];
        var s = new Date(2007, 0, 1);
        var now = new Date(), i = -1;
        while(s.getTime() < now.getTime()){
            var ecount = Ext.ux.getRandomInt(0, 3);
            for(var i = 0; i < ecount; i++){
                var name = Ext.ux.generateName();
                data.push({
                    start : s.clearTime(true).add(Date.DAY, Ext.ux.getRandomInt(0, 27)),
                    name : name,
                    email: name.toLowerCase().replace(' ', '.') + '@exttest.com',
                    active: true,
                    salary: Math.floor(Ext.ux.getRandomInt(35000, 85000)/1000)*1000
                });
            }
            s = s.add(Date.MONTH, 1);
        }
        return data;
    }
*/

    var store = new Ext.data.GroupingStore({
        reader: new Ext.data.JsonReader({fields: Employee}),
        data: cstore,
        sortInfo: {field: 'start', direction: 'ASC'}
    });

    var editor = new Ext.ux.grid.RowEditor({
        saveText: 'Update'
    });

    var grid = new Ext.grid.GridPanel({
        store: store,
        width: 600,
        region:'center',
        margins: '0 5 5 5',
        autoExpandColumn: 'name',
        plugins: [editor],
        view: new Ext.grid.GroupingView({
            markDirty: false
        }),
        tbar: [{
            iconCls: 'icon-user-add',
            text: 'Add Employee',
            handler: function(){
                var e = new Employee({
                    name: 'New Guy',
                    email: 'new@exttest.com',
                    start: (new Date()).clearTime(),
                    salary: 50000,
                    active: true
                });
                editor.stopEditing();
                //store.insert(0, e);
                grid.getView().refresh();
                grid.getSelectionModel().selectRow(0);
                editor.startEditing(0);
            }
        },{
            ref: '../removeBtn',
            iconCls: 'icon-user-delete',
            text: 'Remove Employee',
            disabled: true,
            handler: function(){
                editor.stopEditing();
                var s = grid.getSelectionModel().getSelections();
                for(var i = 0, r; r = s[i]; i++){
                    store.remove(r);
                }
            }
        }],

        columns: [
        new Ext.grid.RowNumberer(),
        {
            id: 'name',
            header: 'First Name',
            dataIndex: 'name',
            width: 220,
            sortable: true,
            editor: {
                xtype: 'textfield',
                allowBlank: false
            }
        },{
            header: 'Email',
            dataIndex: 'email',
            width: 150,
            sortable: true,
            editor: {
                xtype: 'textfield',
                allowBlank: false,
                vtype: 'email'
            }
        },{
            xtype: 'datecolumn',
            header: 'Start Date',
            dataIndex: 'start',
            format: 'm/d/Y',
            width: 100,
            sortable: true,
            groupRenderer: Ext.util.Format.dateRenderer('M y'),
            editor: {
                xtype: 'datefield',
                allowBlank: false,
                minValue: '01/01/2006',
                minText: 'Can\'t have a start date before the company existed!',
                maxValue: (new Date()).format('m/d/Y')
            }
        },{
            xtype: 'numbercolumn',
            header: 'Salary',
            dataIndex: 'salary',
            format: '$0,0.00',
            width: 100,
            sortable: true,
            editor: {
                xtype: 'numberfield',
                allowBlank: false,
                minValue: 1,
                maxValue: 150000
            }
        },{
            xtype: 'booleancolumn',
            header: 'Active',
            dataIndex: 'active',
            align: 'center',
            width: 50,
            trueText: 'Yes',
            falseText: 'No',
            editor: {
                xtype: 'checkbox'
            }
        }]
    });

    var cstore = new Ext.data.JsonStore({
	url: 'http://zend1.grep-r.com/customers/softwareget/id/1',
        fields:['month', 'employees', 'salary'],
    });

    var layout = new Ext.Panel({
        title: 'List of Companies',
        layout: 'border',
        layoutConfig: {
            columns: 1
        },
        width:600,
        height: 600,
        items: [grid]
    });
    layout.render(Ext.getBody());

    grid.getSelectionModel().on('selectionchange', function(sm){
        grid.removeBtn.setDisabled(sm.getCount() < 1);
    });
});
