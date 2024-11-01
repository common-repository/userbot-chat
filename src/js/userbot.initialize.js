document.addEventListener('userbot.ready', function() {
  window.Userbot({
    'key': customer_credentials.key,
    'customerToken': customer_credentials.customer,
    'app': {
      'hostname': 'https://cdn.userbot.ai/widget-chat/dist',
      'socket': 'ai.userbot.ai'
    }
  });
  console.log('key', customer_credentials.key);
  console.log('customer', customer_credentials.customer);
});
