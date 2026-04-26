const ATTACKS = {
  sqli_login: {
    title: "SQL Injection - Login Bypass",
    text: "You bypassed authentication using malicious SQL input.",
    color: "red"
  }
};

function showAttack(name) {
  const attack = ATTACKS[name];
  if (!attack) return;

  const div = document.createElement("div");
  div.innerHTML = `
    <div style="
      position:fixed;top:0;left:0;width:100%;height:100%;
      background:black;color:white;display:flex;
      justify-content:center;align-items:center;
      flex-direction:column;z-index:9999;
    ">
      <h1>${attack.title}</h1>
      <p>${attack.text}</p>
      <button onclick="this.parentElement.parentElement.remove()">Close</button>
    </div>
  `;

  document.body.appendChild(div);
}