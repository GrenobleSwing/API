FROM willdurand/elk:kibana-5.x

RUN /usr/share/logstash/bin/logstash-plugin install logstash-patterns-core
RUN /usr/share/logstash/bin/logstash-plugin update logstash-patterns-core

COPY logstash/logstash.conf /etc/logstash/conf.d/logstash.conf
COPY logstash/patterns /opt/logstash/patterns
