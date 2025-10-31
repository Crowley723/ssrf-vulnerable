# SSRF Exploitable Application
This application aims to provide a good proof of concept for an application vulnerable to Server Side Request Forgery (SSRF).

SSRF occurs when an attacker can manipulate a server into making requests to unintended destinations, including internal-only services, cloud metadata endpoints, or even local system files.

This proof of concept consists of a number of parts.
- Vulnerable App: This is a container with a php app that is vulnerable to SSRF. It takes user input and requests pages from the server. One could request `http://example.com` for example and see the content from that page below a header added by our page.
- Redis is a key-value store commonly used to enable persistent sessions for applications. In our scenario, redis is actually allowing us to create a webshell to allow us to execute commands on the server.
- Internal API: This is another container, it contains a php application meant to represent an internal only endpoint (such as an AWS internal secrets endpoint) with a database (to store sensitive secrets).

## Cyber Kill Chain

Below is the Cyber Kill Chain for our proposed SSRF attack:

1. Reconnaissance
   - Identify the PHP web application with URL fetching functionality
   - Discover that the content retrieval service accepts user-controlled URLs
   - Use initial SSRF to probe internal network structure, identify running internal applications, admin apis, etc
2. Weaponization
   - Prepare a tool like Burp Suite or OWASP Zap to intercept requests and allow modifications
   - Craft SSRF payloads targeting internal services
   - Prepare Gopherus tool and Redis commands for remote code execution
3. Delivery
   - Submit malicious URLs through the vulnerable web application parameter
   - Use Burp Suite to intercept and modify http requests
   - Direct SSRF requests to internal redis instance and admin APIs
4. Exploitation
   - Execute the vulnerability by making the server request internal resources
   - Successfully connect to unprotected Redis instance and admin APIs
   - Exploit admin api to access SQLite database containing credentials
5. Installation
   - Write PHP webshell to web directory via Redis set commands
   - Establish persistent access point through created webshell
   - Gain ability to read/write files on target system
6. Command and Control
   - Access the deployed webshell via HTTP requests.
   - Create an interactive shell connection back to attacker using tools already on target (living on the land)
7. Actions on Objective
   - Exfiltrate sensitive information from SQLite database and file system
   - Steal user passwords and authentication details
   - Demonstrate complete control over the target application and system.
   - If deployed in cloud, use metadata service access for broader infrastructure compromise.