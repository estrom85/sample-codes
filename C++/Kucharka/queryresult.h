#ifndef QUERYRESULT_H
#define QUERYRESULT_H

#include <QSqlQuery>
#include <QVariant>
#include <QString>
#include <QSqlRecord>
#include <QDebug>

class QueryResult
{
private:
    QSqlQuery       query;
    bool            valid;
public:
    QueryResult(QSqlQuery& query);
    QueryResult();

    bool next();

    bool isValid();

    QVariant getValue(int id);
    QVariant getValue(QString &field);
    QVariant getValue(char* field);

    int getValueInt(int id);
    int getValueInt(QString &field);
    int getValueInt(char* field);

    QString getValueString(int id);
    QString getValueString(QString &field);
    QString getValueString(char* field);

    bool getValueBool(int id);
    bool getValueBool(QString &field);
    bool getValueBool(char* field);

private:
    int getFieldId(QString &field);
};

#endif // QUERYRESULT_H
