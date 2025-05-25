// scripts.js

console.log(" scripts.js loaded");

document.addEventListener("DOMContentLoaded", () => {
  console.log(" DOM ready");

  //
  // --REGISTER HANDLER--------------------------------------------------------
  //
  const registerForm = document.getElementById("registerForm");
  if (registerForm) {
    console.log(" found registerForm, wiring up submit");
    registerForm.addEventListener("submit", async e => {
      e.preventDefault();
      console.log(" register submit");

      // gathering register fields
      const payload = {
        full_name:   document.getElementById("full_name").value.trim(),
        email:       document.getElementById("email").value.trim(),
        password:    document.getElementById("password").value,
        phone:       document.getElementById("phone").value.trim(),
        national_id: document.getElementById("national_id").value.trim(),
        prs_id:      document.getElementById("prs_id").value.trim(),
        role_id:     parseInt(document.getElementById("role_id").value, 10)
      };
      console.log("register payload:", payload);

      const url = `${window.location.origin}/prs/api.php/users`;
      const res = await fetch(url, {
        method:  "POST",
        headers: { "Content-Type": "application/json" },
        body:    JSON.stringify(payload)
      });
      const data = await res.json();
      console.log("register response:", data);

      if (data.success) {
        alert("Registration successful!");
        window.location.href = "login.html";
      } else {
        alert("Registration failed: " + (data.error||JSON.stringify(data)));
      }
    });
  }

  //
  // -- LOGIN HANDLER ------------------------------------
  //
  const loginForm = document.getElementById("loginForm");
  if (loginForm) {
    console.log(" found loginForm, wiring up submit");
    loginForm.addEventListener("submit", async e => {
      e.preventDefault();
      console.log(" login submit");

      // gather login fields
      const email    = document.getElementById("email").value.trim();
      const password = document.getElementById("password").value;

      const url = `${window.location.origin}/prs/api.php/login`;
      const res = await fetch(url, {
        method:  "POST",
        headers: { "Content-Type": "application/json" },
        body:    JSON.stringify({ email, password })
      });

      const data = await res.json();
      console.log(" login response:", data);

      if (data.token) {
        // store it or set a cookie, depending on your app
        localStorage.setItem("token", data.token);
        window.location.href = "index.html";
      } else {
        alert("Login failed: " + (data.error||"Unknown error"));
      }
    });
  }
});
