FROM openjdk:8u111-jre-alpine

MAINTAINER Dieter Wimberger "dieter@wimpi.net"

EXPOSE 10389

RUN apk add --no-cache openssl
RUN mkdir /ldap
WORKDIR /ldap
RUN wget https://github.com/intoolswetrust/ldap-server/releases/download/v.1.0.0/ldap-server.jar

COPY users.ldif /ldap/users.ldif

CMD ["java","-jar","ldap-server.jar", "users.ldif"]

