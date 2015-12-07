/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package games_set.card_games32.sedma3;

import games_set.card_games32.CCard;
import games_set.card_games32.CPlayer;

/**
 *
 * @author mato
 */
public class CComputerPlayer extends CPlayer {
    public CComputerPlayer(String name, int index){
        super(name,index);
    }
    
    public int getType(){
        int[] types=new int[4];
        for(int i=0;i<this.getNumberOfCards();i++)
            types[this.getCard(i).getType()]++;
        int max=0;
        int maxIndex = 0;
        for(int i=0;i<4;i++){
            if(types[i]>max){
                max=types[i];
                maxIndex=i;
            }
        }
        //return (int)Math.floor(Math.random()*4);
        return maxIndex;
    }
    //computer AI
    public int getCardIndex(CGameLogic logic){
        int[] types=new int[4];
        int[] numbers=new int[8];
        for(int i=0;i<this.getNumberOfCards();i++){
            types[this.getCard(i).getType()]++;
            numbers[this.getCard(i).getNumber()]++;
        }
        int index=100;
        int maxRate=0;
        for(int i=0;i<this.getNumberOfCards();i++)
        {
            if(logic.isValid(this.getCard(i))){
                int rate=0;
                CCard card=this.getCard(i);
                rate+=types[card.getType()];
                rate+=numbers[card.getNumber()];
                if(card.getNumber()==0)
                    rate+=3;
                if(card.getNumber()==7)
                    rate+=2;
                if(card.getNumber()==5)
                    rate+=1;
                if(rate>maxRate){
                    maxRate=rate;
                    index=i;
                }  
            }
        }
        return index;
    }
}
