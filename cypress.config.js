const { defineConfig } = require("cypress");

module.exports = defineConfig({
  projectId: 'c9snns',
  allowCypressEnv: false,

  e2e: {
    baseUrl: 'http://localhost:8000',
    setupNodeEvents(on, config) {
      // implement node event listeners here
    },
  },
});
