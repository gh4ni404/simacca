describe("Fitur Autentikasi - Aplikasi CI4", () => {
  beforeEach(() => {
    cy.visit("/login");
  });
  it("menampilkan halaman login dengan elemen yang lengkap", function () {
    // cy.prompt([
    //   // --- UI Verification ---
    //   'verify the heading "Login SIMACCA" is visible in the login card',
    //   'verify the subtitle "Silahkan login untuk melanjutkan" is visible',
    //   'verify the username input field with placeholder "Masukkan username" is visible',
    //   'verify the password input field with placeholder "Masukkan password" is visible',
    //   'verify the "Ingat Saya" checkbox is visible',
    //   'verify the "Lupa Password?" link is visible',
    //   'verify the "Login" button at the bottom of the card is visible',
    // ])
    // Prompt step 1: verify the heading "Login SIMACCA" is visible in the login card
    cy.get("div.text-center").contains(/^\s*Login SIMACCA\s*$/).should("be.visible");

    // Prompt step 2: verify the subtitle "Silahkan login untuk melanjutkan" is visible
    cy.contains(/^\s*Silahkan login untuk melanjutkan\s*$/).should("be.visible")

    // Prompt step 3: verify the username input field with placeholder "Masukkan username" is visible
    cy.get("#loginForm div:nth-child(1) > div.relative").find("[name=\"username\"]").should("be.visible")

    // Prompt step 4: verify the password input field with placeholder "Masukkan password" is visible
    cy.get("#loginForm div.space-y-4 > div:nth-child(2)").find("[name=\"password\"]").should("be.visible")

    // Prompt step 5: verify the "Ingat Saya" checkbox is visible
    cy.contains(/^\s*Ingat Saya\s*$/).should("be.visible")

    // Prompt step 6: verify the "Lupa Password?" link is visible
    cy.contains(/^\s*Lupa Password\?\s*$/).should("be.visible")

    // Prompt step 7: verify the "Login" button at the bottom of the card is visible
    cy.get("div.w-full").contains(/^\s*Login\s*$/).should("be.visible")
  });

  it("login gagal", function () {
    // 1. Pengisian form menggunakan cy.prompt
    // cy.prompt([
    //   'type "invalid_username" into the username input field with placeholder "Masukkan username"',
    //   'type "wrong_password" into the password input field with placeholder "Masukkan password"',
    // ])
    // Prompt step 1: type "invalid_username" into the username input field with placeholder "Masukkan username"
    cy.get("#loginForm div:nth-child(1) > div.relative").find("[name=\"username\"]").type("invalid_username");

    // Prompt step 2: type "wrong_password" into the password input field with placeholder "Masukkan password"
    cy.get("#loginForm div:nth-child(2) > div.relative").find("[name=\"password\"]").type("wrong_password")

    // 2. Klik & Assert Loading State secara instan menggunakan Native Cypress
    cy.get("#loginBtn").click();
    cy.get("#loginBtn").should("contain", "Memproses...");

    // 3. Assert SweetAlert instan sebelum auto-close (Native Cypress)
    cy.get(".swal2-title").should("be.visible").and("contain", "Login Gagal");
  });

  it("login success", function () {
    // 1. Pengisian form menggunakan cy.prompt
    // cy.prompt(
    //   [
    //     'type {{username}} into the username input field with placeholder "Masukkan username"',
    //     'type {{password}} into the password input field with placeholder "Masukkan password"',
    //   ],
    //   {
    //     placeholders: {
    //       username: "andimedinanazwaaulia",
    //       password: "0089757772",
    //     },
    //   },
    // )
    // Prompt step 1: type {{username}} into the username input field with placeholder "Masukkan username"
    cy.get("#loginForm div:nth-child(1) > div.relative").find("[name=\"username\"]").type("andimedinanazwaaulia");

    // Prompt step 2: type {{password}} into the password input field with placeholder "Masukkan password"
    cy.get("#loginForm div:nth-child(2) > div.relative").find("[name=\"password\"]").type("0089757772")

    // 2. Klik & Assert Loading State secara instan menggunakan Native Cypress
    cy.get("#loginBtn").click();
    cy.get("#loginBtn").should("contain", "Memproses...");

    // 3. Assert SweetAlert instan sebelum auto-close (Native Cypress)
    cy.get(".swal2-title").should("be.visible").and("contain", "Login Berhasil!");

    // 4. Verifikasi Redirect (URL bersifat permanen sehingga stabil dites dengan cy.prompt)
    // Verifikasi bahwa user sudah berhasil keluar dari halaman login
    cy.prompt(['verify the URL does not include "/login" with a timeout of 10 seconds']);
  });
});