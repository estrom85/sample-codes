#include "dbconnection.h"

/*
  Constructors & Destructors
  */

DBConnection::DBConnection()
{
    //QFile file ("kucharka.db");
    bool fileExists=QFile("kucharka.db").exists();

    mDb=QSqlDatabase::addDatabase("QSQLITE");
    mDb.setDatabaseName("kucharka.db");
    mDb.open();
    query=QSqlQuery(mDb);

    if(!fileExists)
        createDatabase();
}

DBConnection::~DBConnection(){
    mDb.close();
    //QFile("kucharka.db").remove();
}

QueryResult DBConnection::executeSQL(QString &sql){
    QRegExp select("select .{1,} from .{1,}",Qt::CaseInsensitive);
    if(sql.indexOf(select)>-1){
        query.exec(sql);
        return QueryResult(query);
    }
    return QueryResult();
}

int DBConnection::addItem(QString name, bool isCategory, int catId){
    QString sql;
    QTextStream ss(&sql);
    QMessageBox msg;

    if(catId!=0){
        ss<<"SELECT * FROM Items WHERE id="<<catId;
        query.exec(sql);
        if(query.lastError().isValid()){
            msg.setText(query.lastError().text());
            msg.exec();
        }
        if(!query.next()){
            msg.setText(QString("Category does not exist"));
            msg.exec();
            return -1;
        }
        sql.clear();
    }


    ss<<"INSERT INTO Items (name, isCategory, category) VALUES ("
     <<"'"<<name<<"', "<<(isCategory?1:0)<<", "<<catId<<")";

    query.exec(sql);
    if(query.lastError().isValid()){
        msg.setText(query.lastError().text());
        msg.exec();
    }
    return query.lastInsertId().toInt();
}

void DBConnection::editItem(int id, QString name, bool isCategory, int catId){
    QString sql;
    QTextStream ss(&sql);
    QMessageBox msg;

    if(catId!=0){
        ss<<"SELECT * FROM Items WHERE id="<<catId;
        query.exec(sql);
        if(query.lastError().isValid()){
            msg.setText(query.lastError().text());
            msg.exec();
        }
        if(!query.next()){
            msg.setText(QString("Category does not exist: ")+QVariant(catId).toString());
            msg.exec();
            return;
        }
        sql.clear();
    }

    ss<<"UPDATE Items SET "
     <<"name='"<<name<<"', isCategory='"<<(isCategory?1:0)<<"', category='"<<catId<<"' WHERE id="<<id;

    query.exec(sql);
    if(query.lastError().isValid()){
        msg.setText(sql+query.lastError().text());
        msg.exec();
    }
}


int DBConnection::addReceipt(int itemId, QString &ingredients, QString &receipt, QString &image){
    //qDebug()<<"add";
    QString sql;
    QTextStream ss(&sql);
    QMessageBox msg;
    ss<<"SELECT * FROM Items WHERE id="<<itemId;
    query.exec(sql);
    if(query.lastError().isValid()){
        msg.setText(query.lastError().text());
        msg.exec();
    }
    if(!query.next()){
        msg.setText(QString("Item does not exist"));
        msg.exec();
        return -1;
    }

    if(query.value(2).toBool()){
        msg.setText(QString("Item is not receipt. You cannot add receipt to category"));
        msg.exec();
        return -1;
    }

    sql.clear();
    ss<<"INSERT INTO Receipts (id, ingredients, receipt, image) VALUES ("
     <<"'"<<itemId<<"', '"<<ingredients<<"', '"<<receipt<<"','"<<image<<"')";

    query.exec(sql);
    if(query.lastError().isValid()){
        msg.setText(query.lastError().text());
        msg.exec();
    }
    return query.lastInsertId().toInt();
}

void DBConnection::editReceipt(int id, QString &ingredients, QString &receipt, QString &image){
    //qDebug()<<"edit";
    QString sql;
    QTextStream ss(&sql);
    ss<<"UPDATE Receipts SET "
     <<"ingredients='"<<ingredients<<"', receipt='"<<receipt<<"', image='"<<image<<"' WHERE id="<<id;
    QMessageBox msg;
    query.exec(sql);
    if(query.lastError().isValid()){
        msg.setText(sql+query.lastError().text());
        msg.exec();
    }
}

bool DBConnection::removeItem(int id){
    QString sql;
    QTextStream ss(&sql);
    QMessageBox msg;

    //Check if there is the item has any childs. if there item has childs removal
    //is not possible

    sql.clear();
    ss<<"SELECT * FROM Items WHERE category="<<id;
    query.exec(sql);

    if(query.lastError().isValid()){
        msg.setText(sql+query.lastError().text());
        msg.exec();
        return false;
    }

    if(query.next()){
        msg.setText(QString("You can't remove item, remove subcategories first."));
        msg.exec();
        return false;
    }

    //Check type of item. if item is receipt, that removes also record from
    //receipts table

    sql.clear();
    ss<<"SELECT * FROM Items WHERE id="<<id;

    query.exec(sql);
    if(query.lastError().isValid()){
        msg.setText(sql+query.lastError().text());
        msg.exec();
        return false;
    }
    if(!query.next()){
        msg.setText("No record");
        msg.exec();
        return false;
    }

    if(!query.value(2).toBool())
        removeReceipt(id);

    sql.clear();
    ss<<"DELETE FROM Items WHERE id="<<id;
    query.exec(sql);
    if(query.lastError().isValid()){
        msg.setText(sql+query.lastError().text());
        msg.exec();
        return false;
    }
    return true;
}

void DBConnection::removeReceipt(int id){
    QString sql;
    QTextStream ss(&sql);
    ss<<"DELETE FROM Receipts WHERE id="<<id;
    QMessageBox msg;
    query.exec(sql);
    if(query.lastError().isValid()){
        msg.setText(sql+query.lastError().text());
        msg.exec();
    }
}

void DBConnection::createDatabase(){
    //create table Items

    query.exec("CREATE TABLE Items ( "
            "id INTEGER PRIMARY KEY AUTOINCREMENT, "
            "name VARCHAR(200) NOT NULL, "
            "isCategory BOOL NOT NULL, "
            "category INTEGER NOT NULL"
            ")");

    //create table Receipts

    query.exec("CREATE TABLE Receipts ("
            "id INT PRIMARY KEY NOT NULL, "
            "ingredients TEXT NOT NULL, "
            "receipt TEXT NOT NULL, "
            "image VARCHAR(200)"
            ")");

    int lastId=0;

    addItem(QString("Polievky"),1,0);
    lastId=addItem(QString::fromUtf8("Mäso"),1,0);
    addItem(QString::fromUtf8("Kuracie mäso"),1,lastId);
    addItem(QString::fromUtf8("Hovädzie mäso"),1,lastId);
    addItem(QString::fromUtf8("Bravčové mäso"),1,lastId);
    addItem(QString::fromUtf8("Mleté mäso"),1,lastId);
    addItem(QString::fromUtf8("Iné"),1,lastId);

    lastId=addItem(QString::fromUtf8("Bezmäsité jedlá"),1,0);
    addItem(QString::fromUtf8("Múčne jedlá"),1,lastId);
    addItem(QString::fromUtf8("Zelenina"),1,lastId);
    lastId=addItem(QString::fromUtf8("Rôzne"),1,lastId);
/*
    addItem(QString::fromUtf8("Rôzne1"),0,lastId);
    addItem(QString::fromUtf8("Rôzne2"),0,lastId);
    addItem(QString::fromUtf8("Rôzne3"),0,lastId);
*/
    lastId=addItem(QString::fromUtf8("Koláče a zákusky"),1,0);
    addItem(QString::fromUtf8("Kysnuté Koláče"),1,lastId);
    addItem(QString::fromUtf8("Nekysnuté koláče"),1,lastId);
    addItem(QString::fromUtf8("Slané zákusky"),1,lastId);


}


