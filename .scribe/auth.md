# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer {YOUR_AUTH_TOKEN}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

Authenticate using a Sanctum Bearer token. Obtain one from <b>POST /api/register</b> or <b>POST /api/login</b>.
