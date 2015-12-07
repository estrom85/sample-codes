#ifndef DBCONNECTION_H
#define DBCONNECTION_H

#include <QSqlDatabase>
#include <QSqlQuery>
#include <QString>
#include <QFile>
#include <QMessageBox>
#include <QSqlError>
#include <QVariant>
#include <QTextStream>
#include <QRegExp>
#include "queryresult.h"

/*
  Class represents connection to SQLite database. All basic functions to connect to database are available.
  Except of classic sql execution, class provides also basic wrapper functions for insertion, deletion and
  editation of records in database.
  */
class DBConnection
{
private:
    QSqlDatabase    mDb;
    QSqlQuery       query;

public:
    DBConnection();
    ~DBConnection();

public:

    int addItem(QString name, bool isCategory, int catId);
    int addReceipt(int itemId, QString &ingredients, QString &receipt, QString &image);
    void editItem(int id, QString name, bool isCategory, int catId);
    void editReceipt(int id, QString &ingredients, QString &receipt, QString &image);
    bool removeItem(int id);

    QueryResult executeSQL(QString &sql);


private:
    void createDatabase();
    void removeReceipt(int id);


};

#endif // DBCONNECTION_H
