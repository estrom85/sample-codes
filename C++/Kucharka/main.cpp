#include <QtGui/QApplication>
#include "mainwindow.h"
#include "dbconnection.h"
#include <QMessageBox>
#include <QSqlQuery>
#include <QString>
#include <QTextStream>
#include <QDebug>
#include "item.h"

void displayTable(DBConnection &db);
QString displayList(Item *item, QString separator);

int main(int argc, char *argv[])
{
    QApplication a(argc, argv);
    MainWindow w;
    w.setWindowTitle(QString::fromUtf8("Moja Kuchárka v 0.2 beta"));
    w.show();
    /*
    DBConnection db;


    Item root(db);
    //qDebug()<<"items created";
    QString display=displayList(&root,QString());
    QMessageBox msg;
    msg.setText(display);
    msg.exec();
    */
    return a.exec();
}

void displayTable(DBConnection &db){
    QString sql="SELECT * FROM Items";
    //QString sql="DELETE FROM Items WHERE id=5";
    QueryResult query=db.executeSQL(sql);
    QString output;
    QMessageBox msg;
    QTextStream ss(&output);
    if(!query.isValid()){
        msg.setText("Not select");
        msg.exec();
        return;
    }
    while(query.next()){
        ss<<query.getValueString("name")<<"\t"<<query.getValueString("id")<<"\t"<<query.getValueString("isCategory")<<"\t"<<query.getValueString("category")
         <<"\n";
    }


    msg.setText(output);
    msg.exec();
}

QString displayList(Item* item, QString separator){
    if(item==0)
        return QString();
    //qDebug()<<item;
    int i=0;
    Item* child=0;
    QString output;
    output+=separator+item->getName()+QString("\n");
    separator+=QString("-");

    while(true){
        //qDebug()<<"enter loop";
        child=item->getChild(i);
        //qDebug()<<"child received: "<<child;
        i++;
        if(child==0)
            break;
        output+=displayList(child,separator);
    }
    return output;
}
