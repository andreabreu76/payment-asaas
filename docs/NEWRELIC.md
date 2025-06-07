# NewRelic Integration

This document explains how to use the NewRelic integration with the Payment Asaas API application.

## Overview

The application is integrated with NewRelic for monitoring and observability. NewRelic provides:

- Application performance monitoring
- Error tracking
- Transaction tracing
- Custom metrics
- Alerts and notifications

## Configuration

### Environment Variables

The following environment variables need to be set in your `.env` file:

```
# NewRelic Configuration
NEW_RELIC_LICENSE_KEY=your_license_key_here
NEW_RELIC_APP_NAME="Payment Asaas API"
NEW_RELIC_DAEMON_ADDRESS=newrelic:31339
```

Replace `your_license_key_here` with your actual NewRelic license key. You can find this in your NewRelic account settings.

### Docker Containers

The application uses the following NewRelic-related containers:

1. **newrelic**: The NewRelic daemon container that collects and forwards metrics to NewRelic
2. **php**: The main application container with NewRelic PHP agent installed
3. **worker**: The queue worker container with NewRelic PHP agent installed
4. **horizon**: The Laravel Horizon container with NewRelic PHP agent installed

## Accessing NewRelic Dashboard

1. Log in to your NewRelic account at [https://login.newrelic.com/](https://login.newrelic.com/)
2. Navigate to the APM (Application Performance Monitoring) section
3. Select your application (as defined by `NEW_RELIC_APP_NAME` in your `.env` file)

You should see three separate applications in your NewRelic dashboard:
- Payment Asaas API (main application)
- Payment Asaas API Worker (queue worker)
- Payment Asaas API Horizon (Laravel Horizon)

## Viewing Logs

NewRelic logs are stored in a Docker volume named `newrelic-logs`. You can access these logs by:

1. Connecting to the NewRelic container:
   ```
   docker exec -it payment-asaas-newrelic /bin/bash
   ```

2. Viewing the logs:
   ```
   cat /var/log/newrelic/newrelic-daemon.log
   ```

## Troubleshooting

If you're not seeing data in your NewRelic dashboard:

1. Verify that your license key is correct in the `.env` file
2. Check the NewRelic daemon logs for any errors:
   ```
   docker logs payment-asaas-newrelic
   ```
3. Verify that the PHP containers can connect to the NewRelic daemon:
   ```
   docker exec -it payment-asaas-php ping newrelic
   ```
4. Check the PHP container logs for any NewRelic-related errors:
   ```
   docker logs payment-asaas-php | grep newrelic
   ```

## Additional Resources

- [NewRelic PHP Agent Documentation](https://docs.newrelic.com/docs/apm/agents/php-agent/getting-started/introduction-new-relic-php/)
- [NewRelic APM Documentation](https://docs.newrelic.com/docs/apm/)
- [NewRelic Logs Documentation](https://docs.newrelic.com/docs/logs/)
