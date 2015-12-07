#ifndef ITEM_H
#define ITEM_H

/*******************************************************************************************/
/**********************************  Class Item   ******************************************/
/*******************************************************************************************/

/*
  Popis:
  Trieda Item reprezentuje základnú položku dátovej štruktúry programu. Všetky kategórie a recepty
  v programe sú uložené v stromovej štruktúre kde pod každou kategóriou sú uložené ďalšie kategórie
  a recepty. Táto štruktúra je generovaná z údajov z databázy. Základná funkcionalita spočíva v
  manipulácii so stromovou štrukútrov a následnou synchronizáciou databázy s aktuálnou štruktúrov
  Každá zmena v strome je zaznamenávaná do databázy.

  Autor:
  Ing. Martin Mačaj

  Licenčné podmienky:

  Dátum poslednej úpravy:
  2/3/2013

  */

/* QT Interface */

#include <QString>
#include <QList>
#include <QString>
#include <QTextStream>
#include <QMessageBox>

/* Program interface */

#include "dbconnection.h"
#include "queryresult.h"
#include "receiptdescription.h"

/* Debugging */

#include <QDebug>



class Item
{
    /*
      Verejné rozhranie triedy
      */
public:
    /*
        Konštruktor a deštruktor

        Položka je vytvorená na základe údajov z databázy. Vytvorí položku a všetky
        dcérske položky, ktorých ukazovatele uloží do kontainera.

        Paremetre:
        DBConnection &db    - referencia na objekt DBConnection ktorá reprezentuje pripojenie na databázu - povinná položka
        Item* parent        - ukazovateľ na materskú položku. Ak je položka koreň (root) hodnota je nulový ukazovateľ
                            - prednastavená hodnota 0
        int id              - id položky v databáze, ak je položka koreň (root) hodnota je rovná 0
                            - prednastavená hodnota 0
    */
    Item(DBConnection &db, Item* parent=0, int id=0);
    ~Item();

    /*
        Gettery

        Rozranie pre získavanie informácií z triedy
    */

    QString getName();          //vráti názov položky
    Item* getChild(int i);      //vráti ukazovateľ na i-tu dcérsku položku
    int getId();                //vráti id triedy
    bool isCategory();          //informuje, či je položka kategória

    Item* getParent();          //vráti ukazovateľ na materskú položku
    int getNumRows();           //vráti počet dcérskych položiek
    int getRow();               //vráti pozíciu položky v kontajneri materskej položky

    /*
        Rozhranie pre manipuláciu so stromom

        Toto rozhranie využíva databázové pripojenie na synchronizáciu údajov v databáze so zmenami
        v stromovej štruktúre. Každá zmena položky je hneď zaznamenávaná do databázy.
    */

    // Pridá dcérsku položku - prednastavené údaje
    bool addChild(DBConnection &db);

    //Odstráni dcérsku položku
    bool removeChild(DBConnection &db, int i);

    //zmení názov dcérskej položky
    bool editChild(DBConnection &db, int i, QString &name);

    //vytvorí a priradí recept k položke
    bool addReceipt(DBConnection &db);

    //presunie polozku
    bool moveItem(DBConnection &db, Item* newParent);

    QList<Item*> getList();
    /*
      Parametre triedy
      */
private:
    int             mId;            //identifikačné číslo položky v databáze
                                    //slúži na vyhľadanie všetkých údajov položky v databáze
    QString         name;           //názov položky
    bool            mIsCategory;    //indikátor kategórie - ak je položka kategória, hodnota parametra je "true"

    QList<Item*>    childList;      //kontajner, ktorý ukladá ukazovatele na dcérske položky ("childs") stromu
    Item*           parent;         //ukazovateľ na materskú položku

    QMessageBox     msg;

    /*
      Súkromné metódy triedy
      */

private:

    //nastaví názov položky
    void setName(QString &name);

};

#endif // ITEM_H
