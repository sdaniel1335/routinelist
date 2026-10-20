const fs = require('fs');
const vm = require('vm');
const assert = require('assert');
let handler;
const fields = ['weekly odd even quarterly','monthly halfyear yearly','halfyear yearly','quarterly','season'].map(rules => ({hidden:false,getAttribute:()=>rules,inputs:[{}],querySelectorAll(){return this.inputs;}}));
const controls = {'routine-rule':{value:'workday',addEventListener(){}},'routine-weekday':{value:'0',options:[{}]},'routine-day':{value:'0'},'routine-month':{value:'12'}};
let accepted=false;
const context={document:{getElementById:id=>controls[id],querySelectorAll:()=>fields,addEventListener:(event,fn)=>{handler=fn;}},window:{confirm:()=>accepted},Intl,Date};
vm.createContext(context);
vm.runInContext(fs.readFileSync(require('path').join(__dirname,'../public_html/js/app.js'),'utf8'),context);
context.App.bindUIActions();
for (const rule of ['workday','restday','daily','weekly','odd','even','monthly','quarterly','halfyear','yearly','season']) {
  controls['routine-rule'].value=rule; context.App.updateRuleFields();
  fields.forEach(field=>{const visible=field.getAttribute().split(' ').includes(rule); assert.strictEqual(field.hidden,!visible); assert.strictEqual(field.inputs[0].disabled,!visible); assert.strictEqual(field.inputs[0].required,visible);});
}
controls['routine-rule'].value='halfyear'; controls['routine-month'].value='12'; context.App.updateRuleFields();
assert.strictEqual(controls['routine-month'].value,'1'); assert.strictEqual(controls['routine-day'].min,'0');
controls['routine-rule'].value='weekly'; controls['routine-weekday'].value='0'; context.App.updateRuleFields(); assert.strictEqual(controls['routine-weekday'].value,'1');
let prevented=false;
const form={hasAttribute:()=>true,getAttribute:()=>'Törlés?'};
handler({target:form,preventDefault(){prevented=true;}}); assert.strictEqual(prevented,true);
accepted=true; prevented=false; handler({target:form,preventDefault(){prevented=true;}}); assert.strictEqual(prevented,false);
console.log('Passed: 11 recurrence form modes, field bounds, and deletion confirmation cancel/accept.');
