import { createProxyMiddleware } from 'http-proxy-middleware';
import { Express } from 'express';

module.exports = function (app: Express) {
  app.use(
    '/api',
    createProxyMiddleware({
      target: 'https://174bef48-1d86-4312-8eac-ede07fbd236e-00-o3f3t4j7n7tx.picard.replit.dev:8000',
      changeOrigin: true,
      pathRewrite: { '^/api': '' },
    })
  );
};
