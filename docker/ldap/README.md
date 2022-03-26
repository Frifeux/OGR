https://github.com/intoolswetrust/ldap-server

```bash
docker build -t dwimberger/ldap-ad-it .
docker run -it --rm -p 10389:10389 dwimberger/ldap-ad-it
```

# Docker-compose file
```yaml
version: '3.8'
services:
  ldap:
    container_name: ldap_symfony
    build:
      context: ./docker/ldap
    ports:
      - '10389:10389'
    restart: always
```


# Voir les logs de connexion

```bash
docker logs ldap_symfony
```

```
You can connect to the server now
URL:      ldap://127.0.0.1:10389
User DN:  uid=admin,ou=system
Password: secret
```

# Naviger dans le LDAP
https://directory.apache.org/studio/
- Logiciel: ApacheDirectoryStudio