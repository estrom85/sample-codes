#include "queryresult.h"
#include <QSqlError>
QueryResult::QueryResult(QSqlQuery &query){

    if(query.isSelect()){
        this->valid=true;
        this->query=query;
    }
    else
        this->valid=false;
}
QueryResult::QueryResult(){
    this->valid=false;
}

bool QueryResult::next(){
    if(!valid)
        return false;
    return query.next();
}

bool QueryResult::isValid(){
    /*
    if(query)
        return true;

    return false;
    */
    return valid;
}

QVariant QueryResult::getValue(int id){
    if(valid||id>0)
        return query.value(id);
    return QVariant();
}

QVariant QueryResult::getValue(QString &field){
    return getValue(getFieldId(field));
}
QVariant QueryResult::getValue(char *field){
    QString f(field);
    return getValue(f);
}

int QueryResult::getValueInt(int id){
    return getValue(id).toInt();
}

int QueryResult::getValueInt(QString &field){
    return getValueInt(getFieldId(field));
}

int QueryResult::getValueInt(char *field){
    QString f(field);
    return getValueInt(f);
}

QString QueryResult::getValueString(int id){
    return getValue(id).toString();
}

QString QueryResult::getValueString(QString &field){
    return getValueString(getFieldId(field));
}

QString QueryResult::getValueString(char *field){
    QString f(field);
    return getValueString(f);
}

bool QueryResult::getValueBool(int id){
    return getValue(id).toBool();
}

bool QueryResult::getValueBool(QString &field){
    return getValueBool(getFieldId(field));
}

bool QueryResult::getValueBool(char *field){
    QString f(field);
    return getValueBool(f);
}

int QueryResult::getFieldId(QString &field){
    if(!isValid())
        return -1;
    return query.record().indexOf(field);
}
