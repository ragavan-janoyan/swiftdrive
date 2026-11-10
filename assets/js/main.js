// Mobile menu toggle
const mb=document.getElementById('menuBtn');if(mb)mb.onclick=()=>document.getElementById('menu').classList.toggle('open');

// Live price estimate + client-side date validation on the booking form
const bf=document.getElementById('bookForm');
if(bf){
  const p=bf.pickup_date,r=bf.return_date,out=document.getElementById('estimate');
  const daily=+bf.dataset.daily,weekly=+bf.dataset.weekly;
  const today=new Date().toISOString().split('T')[0];p.min=today;r.min=today;
  function calc(){
    if(!p.value||!r.value){out.textContent='Select dates to see the estimate';return true;}
    const d=Math.round((new Date(r.value)-new Date(p.value))/864e5);
    if(d<1){out.textContent='Return date must be after pick-up date';return false;}
    const total=Math.floor(d/7)*weekly+(d%7)*daily;
    out.textContent=d+' day(s) · Total Rs. '+total.toLocaleString()+' · Deposit (20%) Rs. '+(total*.2).toLocaleString();return true;
  }
  p.onchange=()=>{r.min=p.value;calc();};r.onchange=calc;
  bf.onsubmit=e=>{if(!calc()){e.preventDefault();alert('Please choose valid rental dates.');}};calc();
}

// Payment form: format card number, basic checks
const cn=document.getElementById('card');
if(cn)cn.oninput=()=>{cn.value=cn.value.replace(/\D/g,'').slice(0,16).replace(/(.{4})/g,'$1 ').trim();};

// Confirm destructive actions
document.querySelectorAll('[data-confirm]').forEach(el=>el.addEventListener('click',e=>{if(!confirm(el.dataset.confirm))e.preventDefault();}));
