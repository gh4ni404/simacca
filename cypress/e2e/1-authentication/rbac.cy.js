describe("Fitur Autentikasi Multi-Role - Aplikasi SIMACCA", () => {
  beforeEach(() => {
    cy.visit("/login");
  });

  // --- 1. ADMIN ---
  it("Login Berhasil - Admin", () => {
    cy.prompt(
      [
        'type {{username}} into the username input field with placeholder "Masukkan username"',
        'type {{password}} into the password input field with placeholder "Masukkan password"',
      ],
      {
        placeholders: { username: "admin", password: "smart123" },
      },
    );

    cy.get("#loginBtn").click();
    cy.get(".swal2-title").should("be.visible").and("contain", "Login Berhasil!");

    cy.prompt(['verify the URL includes "/admin/dashboard" with a timeout of 10 seconds', "verify the welcome card is visible"]);
  });

  // --- 2. GURU MAPEL ---
  it("Login Berhasil - Guru Mapel", () => {
    cy.prompt(
      [
        'type {{username}} into the username input field with placeholder "Masukkan username"',
        'type {{password}} into the password input field with placeholder "Masukkan password"',
      ],
      {
        placeholders: { username: "guru10", password: "guru123" },
      },
    );

    cy.get("#loginBtn").click();
    cy.get(".swal2-title").should("be.visible").and("contain", "Login Berhasil!");

    cy.prompt(['verify the URL includes "/guru/dashboard" with a timeout of 10 seconds', "verify the welcome card is visible"]);
  });

  // --- 3. GURU WALI KELAS ---
  it("Login Berhasil - Guru Wali Kelas", () => {
    cy.prompt(
      [
        'type {{username}} into the username input field with placeholder "Masukkan username"',
        'type {{password}} into the password input field with placeholder "Masukkan password"',
      ],
      {
        placeholders: { username: "idaariani18", password: "Ida08041994" },
      },
    );

    cy.get("#loginBtn").click();
    cy.get(".swal2-title").should("be.visible").and("contain", "Login Berhasil!");

    cy.prompt(['verify the URL includes "/walikelas/dashboard" with a timeout of 10 seconds', "verify the welcome card is visible"]);
  });

  // --- 4. KETUA JURUSAN ---
  it("Login Berhasil - Ketua Jurusan", () => {
    cy.prompt(
      [
        'type {{username}} into the username input field with placeholder "Masukkan username"',
        'type {{password}} into the password input field with placeholder "Masukkan password"',
      ],
      {
        placeholders: { username: "guru6", password: "smart123" },
      },
    );

    cy.get("#loginBtn").click();
    cy.get(".swal2-title").should("be.visible").and("contain", "Login Berhasil!");

    cy.prompt(['verify the URL includes "/ketua-jurusan/dashboard" with a timeout of 10 seconds', "verify the welcome card is visible"]);
  });

  // --- 5. WAKIL KURIKULUM ---
  it("Login Berhasil - Wakil Kurikulum", () => {
    cy.prompt(
      [
        'type {{username}} into the username input field with placeholder "Masukkan username"',
        'type {{password}} into the password input field with placeholder "Masukkan password"',
      ],
      {
        placeholders: { username: "guru15", password: "guru123" },
      },
    );

    cy.get("#loginBtn").click();
    cy.get(".swal2-title").should("be.visible").and("contain", "Login Berhasil!");

    cy.prompt(['verify the URL includes "/wakakur/dashboard" with a timeout of 10 seconds', "verify the welcome card is visible"]);
  });

  // --- 6. INSTRUKTUR PKL ---
  it("Login Berhasil - Instruktur PKL", () => {
    cy.prompt(
      [
        'type {{username}} into the username input field with placeholder "Masukkan username"',
        'type {{password}} into the password input field with placeholder "Masukkan password"',
      ],
      {
        placeholders: { username: "dirwanjaya2609", password: "dirwanjaya2609" },
      },
    );

    cy.get("#loginBtn").click();
    cy.get(".swal2-title").should("be.visible").and("contain", "Login Berhasil!");

    cy.prompt(['verify the URL includes "/instruktur/dashboard" with a timeout of 10 seconds', "verify the welcome card is visible"]);
  });

  // --- 7. SISWA KELAS 12 (PKL) ---
  it("Login Berhasil - Siswa PKL", () => {
    cy.prompt(
      [
        'type {{username}} into the username input field with placeholder "Masukkan username"',
        'type {{password}} into the password input field with placeholder "Masukkan password"',
      ],
      {
        placeholders: { username: "andimedinanazwaaulia", password: "0089757772" },
      },
    );

    cy.get("#loginBtn").click();
    cy.get(".swal2-title").should("be.visible").and("contain", "Login Berhasil!");

    cy.prompt(['verify the URL includes "/siswa/jurnal-pkl" with a timeout of 10 seconds', "verify the welcome card is visible"]);
  });
});
