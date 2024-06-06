create table honeypot.logs
(
    `utc_time`          DATETIME(6) PRIMARY KEY,
    dst_host            varchar(255)    not null,
    dst_port            INTEGER not null,
    local_time          DATETIME    not null,
    local_time_adjusted DATETIME    not null,
    local_version       varchar(255),
    password            varchar(255),
    remote_version      varchar(255),
    username            varchar(255),
    honeycred           BOOLEAN,
    logtype             INTEGER not null,
    node_id             varchar(255)    not null,
    src_host            varchar(255)    not null,
    src_port            INTEGER not null,
    continent           varchar(255),
    country_code        varchar(255),
    country             varchar(255),
    city                varchar(255),
    latitude            float,
    longitude           float
);

